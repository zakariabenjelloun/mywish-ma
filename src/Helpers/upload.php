<?php
declare(strict_types=1);

/**
 * File upload helpers.
 *
 * Loaded in public/index.php bootstrap (alongside functions.php).
 * Uses GD natively (no Composer / no Imagick) — requires the gd PHP extension.
 *
 * Storage layout (under BASE_PATH):
 *   /storage/uploads/events/{eventId}/event-{eventId}-{uniqid}.jpg
 *
 * Public URL is the same path (since BASE_PATH = PUBLIC_PATH on cPanel deploy).
 * ⚠ Verify that public/.htaccess does NOT block /storage/uploads/*.
 */

if (!function_exists('uploadEventPhoto')) {
    /**
     * Validate, compress and store an event-scoped photo (cagnotte or hero).
     *
     * Rules:
     *   - MIME (via finfo, not client-supplied): image/jpeg or image/png only
     *   - Size: max 5 MB
     *   - Output: always JPEG quality 85, max 1200px wide (height auto)
     *   - PNG transparency → flat white background (JPEG has no alpha)
     *
     * @param array $file  One $_FILES entry (keys: tmp_name, error, size, ...).
     * @param int   $eventId
     * @return string|null Public URL on success, null on validation failure.
     * @throws \RuntimeException on filesystem errors (mkdir/imagejpeg).
     */
    function uploadEventPhoto(array $file, int $eventId): ?string
    {
        // ── 1. Basic upload sanity ────────────────────────────
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }
        if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return null;
        }
        if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
            return null;
        }

        // ── 2. Verify MIME via finfo (NOT the client-supplied $file['type']) ──
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if (!in_array($mime, ['image/jpeg', 'image/png'], true)) {
            return null;
        }

        // ── 3. Decode with GD (also catches truncated/corrupt files) ──
        $src = $mime === 'image/png'
            ? @imagecreatefrompng($file['tmp_name'])
            : @imagecreatefromjpeg($file['tmp_name']);
        if ($src === false) {
            return null;
        }

        // ── 4. Resize to ≤ 1200px wide + flatten transparency if PNG ──
        $width  = imagesx($src);
        $height = imagesy($src);
        $maxW   = 1200;

        $needsRewrite = ($width > $maxW) || ($mime === 'image/png');
        if ($needsRewrite) {
            $ratio = $width > $maxW ? ($maxW / $width) : 1.0;
            $newW  = (int) round($width  * $ratio);
            $newH  = (int) round($height * $ratio);

            $dst = imagecreatetruecolor($newW, $newH);
            $white = imagecolorallocate($dst, 255, 255, 255);
            imagefilledrectangle($dst, 0, 0, $newW, $newH, $white);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $width, $height);
            imagedestroy($src);
            $src = $dst;
        }

        // ── 5. Ensure target dir exists ──────────────────────
        $relativeDir = '/storage/uploads/events/' . $eventId;
        $absoluteDir = BASE_PATH . $relativeDir;
        if (!is_dir($absoluteDir)) {
            if (!@mkdir($absoluteDir, 0775, true) && !is_dir($absoluteDir)) {
                imagedestroy($src);
                throw new \RuntimeException("Cannot create upload directory: {$absoluteDir}");
            }
        }

        // ── 6. Save as JPEG quality 85 ───────────────────────
        $filename     = 'event-' . $eventId . '-' . uniqid() . '.jpg';
        $absolutePath = $absoluteDir . '/' . $filename;
        $ok = imagejpeg($src, $absolutePath, 85);
        imagedestroy($src);

        if (!$ok) {
            throw new \RuntimeException("Failed to save image: {$absolutePath}");
        }

        return $relativeDir . '/' . $filename;
    }
}
