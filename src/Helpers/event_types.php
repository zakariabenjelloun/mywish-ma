<?php
declare(strict_types=1);

/**
 * Event type helpers — French labels + default-text generators.
 *
 * Loaded in public/index.php bootstrap (alongside functions.php).
 * Pure functions: no DB, no state.
 *
 * The $names array shape per event type (Controller's responsibility):
 *   wedding/engagement: ['primary' => 'Sara',    'secondary' => 'Yassine']
 *   anniversary:        ['primary' => 'Ibrahim', 'age' => 7]   (age optional)
 *   birth:              ['primary' => 'Adam',    'secondary' => 'Famille Bennani']
 *   other:              ['primary' => 'Soirée gala']
 */

if (!function_exists('eventTypeLabel')) {
    /** French label for an event type enum value. Returns input unchanged if unknown. */
    function eventTypeLabel(string $type): string
    {
        return [
            'wedding'     => 'Mariage',
            'anniversary' => 'Anniversaire',
            'birth'       => 'Naissance',
            'engagement'  => 'Fiançailles',
            'other'       => 'Autre',
        ][$type] ?? $type;
    }
}

if (!function_exists('cagnotteTypeLabel')) {
    function cagnotteTypeLabel(string $type): string
    {
        return [
            'travel'    => 'Voyage',
            'furniture' => 'Mobilier',
            'free_gift' => 'Cadeau libre',
            'other'     => 'Autre',
        ][$type] ?? $type;
    }
}

if (!function_exists('generateDefaultTitle')) {
    /** Suggested page title built from event type + names. */
    function generateDefaultTitle(string $type, array $names): string
    {
        $primary   = trim((string) ($names['primary']   ?? ''));
        $secondary = trim((string) ($names['secondary'] ?? ''));
        $age       = isset($names['age']) ? (int) $names['age'] : null;

        switch ($type) {
            case 'wedding':
                return $primary && $secondary
                    ? "Le mariage de {$primary} & {$secondary}"
                    : 'Notre mariage';
            case 'engagement':
                return $primary && $secondary
                    ? "Les fiançailles de {$primary} & {$secondary}"
                    : 'Nos fiançailles';
            case 'anniversary':
                if ($primary && $age !== null) {
                    return "Les {$age} ans " . _articleDe($primary) . $primary;
                }
                return $primary
                    ? "L'anniversaire " . _articleDe($primary) . $primary
                    : 'Mon anniversaire';
            case 'birth':
                return $primary
                    ? "Bienvenue à {$primary}"
                    : 'Bienvenue au petit nouveau';
            case 'other':
            default:
                return $primary !== '' ? $primary : 'Mon événement';
        }
    }
}

if (!function_exists('generateWelcomeMessage')) {
    /**
     * Pre-written welcome message — 5 types × 3 tones = 15 templates.
     * Returns empty string if the type/tone combination is unknown.
     */
    function generateWelcomeMessage(string $type, array $names, string $tone): string
    {
        $primary   = trim((string) ($names['primary']   ?? ''));
        $secondary = trim((string) ($names['secondary'] ?? ''));
        $age       = isset($names['age']) ? (int) $names['age'] : null;
        $agePhrase = $age !== null ? "les {$age} ans" : "l'anniversaire";

        $templates = [
            'wedding' => [
                'formal' => "{primary} & {secondary} vous prient de leur faire l'honneur de votre présence à leur mariage. Votre présence sera un précieux souvenir pour ce jour unique.",
                'warm'   => "{primary} & {secondary} sont heureux de vous inviter à célébrer leur mariage. Venez partager avec nous ce moment de bonheur, en famille et entre amis.",
                'casual' => "On se marie ! {primary} & {secondary}. Venez fêter ça avec nous, ça va être une journée inoubliable.",
            ],
            'engagement' => [
                'formal' => "{primary} & {secondary} ont l'honneur de vous convier à la célébration de leurs fiançailles. Votre présence honorera ce moment important pour nos deux familles.",
                'warm'   => "{primary} & {secondary} sont heureux de vous inviter à célébrer leurs fiançailles. Une étape importante à partager avec ceux qui comptent.",
                'casual' => "{primary} & {secondary} se fiancent ! Venez célébrer cette belle étape avec nous.",
            ],
            'anniversary' => [
                'formal' => "Vous êtes cordialement invité à célébrer {agePhrase} {de}{primary}. Votre présence sera un cadeau précieux.",
                'warm'   => "Nous fêtons {agePhrase} {de}{primary} ! Venez partager ce moment de joie en famille et entre amis.",
                'casual' => "C'est l'anniversaire {de}{primary} ! Venez fêter ça avec nous.",
            ],
            'birth' => [
                'formal' => "Nous avons l'immense bonheur de vous annoncer la naissance {de}{primary}. Vous êtes cordialement invité à célébrer cette joie avec notre famille.",
                'warm'   => "{primary} est arrivé(e) parmi nous ! Toute la famille est aux anges. Venez partager ce moment magique.",
                'casual' => "Bienvenue à {primary} ! Venez faire connaissance avec le petit nouveau.",
            ],
            'other' => [
                'formal' => "Vous êtes cordialement invité à cet événement spécial. Votre présence nous honorera.",
                'warm'   => "Nous serions ravis de partager ce moment avec vous. À très bientôt !",
                'casual' => "On organise un truc cool. Viens, ça va être sympa !",
            ],
        ];

        $template = $templates[$type][$tone] ?? '';
        return strtr($template, [
            '{primary}'   => $primary,
            '{secondary}' => $secondary,
            '{agePhrase}' => $agePhrase,
            '{de}'        => $primary !== '' ? _articleDe($primary) : 'de ',
        ]);
    }
}

if (!function_exists('_articleDe')) {
    /**
     * French elision helper. Returns "d'" if $name starts with a vowel/h/y,
     * "de " otherwise. Used for "Les X ans d'Ibrahim" vs "Les X ans de Sara".
     */
    function _articleDe(string $name): string
    {
        if ($name === '') return 'de ';
        $first  = mb_strtolower(mb_substr($name, 0, 1), 'UTF-8');
        $vowels = ['a', 'e', 'i', 'o', 'u', 'y', 'h', 'à', 'â', 'é', 'è', 'ê', 'î', 'ï', 'ô', 'û'];
        return in_array($first, $vowels, true) ? "d'" : 'de ';
    }
}
