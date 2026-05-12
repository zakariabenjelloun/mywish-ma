<?php $flashError = flash('error'); ?>

<section class="container-mywish max-w-3xl mx-auto py-10 md:py-16" x-data="wizard()" x-cloak>

    <?php if ($flashError): ?>
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 text-sm">
            <?= e($flashError) ?>
        </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="font-display font-extrabold text-3xl md:text-4xl tracking-tight mb-2">
            Créer mon événement
        </h1>
        <p class="text-text-secondary">4 étapes, 2 minutes, une page partageable.</p>
    </div>

    <!-- Progress -->
    <div class="mb-8">
        <div class="flex justify-between items-baseline mb-2">
            <span class="text-xs font-semibold uppercase tracking-widest text-text-muted">
                Étape <span class="text-primary-soft" x-text="step"></span>/4
            </span>
            <span class="text-xs text-text-muted" x-text="stepLabel(step)"></span>
        </div>
        <div class="w-full bg-bg-higher rounded-full h-2 overflow-hidden">
            <div class="h-full transition-all duration-300 bg-gradient-to-r from-primary-deep via-primary to-gold"
                 :style="`width: ${(step/4)*100}%`"></div>
        </div>
    </div>

    <form method="POST" action="/events" enctype="multipart/form-data" @submit="onSubmit($event)">
        <?= csrf_field() ?>

        <!-- ═══════ STEP 1 — Qui ? ═══════ -->
        <div x-show="step === 1" x-transition.opacity>
            <div class="card">
                <h2 class="font-display font-bold text-2xl mb-2">Félicitations ! C'est pour quel événement ?</h2>
                <p class="text-text-secondary text-sm mb-6">Choisissez le type d'événement à célébrer.</p>

                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-6">
                    <!-- wedding -->
                    <button type="button" @click="type = 'wedding'"
                            class="rounded-xl p-4 flex flex-col items-center gap-2 transition-colors cursor-pointer border-2"
                            :class="type === 'wedding' ? 'border-primary bg-bg-high' : 'border-bg-higher bg-bg-raised hover:border-primary/40'">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" :class="type === 'wedding' ? 'text-primary-soft' : 'text-text-secondary'">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                        <span class="text-sm font-semibold">Mariage</span>
                    </button>

                    <!-- anniversary -->
                    <button type="button" @click="type = 'anniversary'"
                            class="rounded-xl p-4 flex flex-col items-center gap-2 transition-colors cursor-pointer border-2"
                            :class="type === 'anniversary' ? 'border-primary bg-bg-high' : 'border-bg-higher bg-bg-raised hover:border-primary/40'">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" :class="type === 'anniversary' ? 'text-primary-soft' : 'text-text-secondary'">
                            <path d="M20 21v-8a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8"/>
                            <path d="M4 16s.5-1 2-1 2.5 2 4 2 2.5-2 4-2 2.5 2 4 2 2-1 2-1"/>
                            <path d="M2 21h20"/>
                            <path d="M7 8v3"/><path d="M12 8v3"/><path d="M17 8v3"/>
                            <path d="M7 4h.01"/><path d="M12 4h.01"/><path d="M17 4h.01"/>
                        </svg>
                        <span class="text-sm font-semibold">Anniversaire</span>
                    </button>

                    <!-- birth -->
                    <button type="button" @click="type = 'birth'"
                            class="rounded-xl p-4 flex flex-col items-center gap-2 transition-colors cursor-pointer border-2"
                            :class="type === 'birth' ? 'border-primary bg-bg-high' : 'border-bg-higher bg-bg-raised hover:border-primary/40'">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" :class="type === 'birth' ? 'text-primary-soft' : 'text-text-secondary'">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                            <line x1="9" y1="9" x2="9.01" y2="9"/>
                            <line x1="15" y1="9" x2="15.01" y2="9"/>
                        </svg>
                        <span class="text-sm font-semibold">Naissance</span>
                    </button>

                    <!-- engagement -->
                    <button type="button" @click="type = 'engagement'"
                            class="rounded-xl p-4 flex flex-col items-center gap-2 transition-colors cursor-pointer border-2"
                            :class="type === 'engagement' ? 'border-primary bg-bg-high' : 'border-bg-higher bg-bg-raised hover:border-primary/40'">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" :class="type === 'engagement' ? 'text-primary-soft' : 'text-text-secondary'">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        <span class="text-sm font-semibold">Fiançailles</span>
                    </button>

                    <!-- other -->
                    <button type="button" @click="type = 'other'"
                            class="rounded-xl p-4 flex flex-col items-center gap-2 transition-colors cursor-pointer border-2"
                            :class="type === 'other' ? 'border-primary bg-bg-high' : 'border-bg-higher bg-bg-raised hover:border-primary/40'">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" :class="type === 'other' ? 'text-primary-soft' : 'text-text-secondary'">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                        <span class="text-sm font-semibold">Autre</span>
                    </button>
                </div>

                <!-- Wedding / Engagement names -->
                <div x-show="type === 'wedding' || type === 'engagement'" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2"
                               x-text="type === 'wedding' ? 'Nom de la mariée' : 'Nom de la fiancée'"></label>
                        <input type="text" name="mariee_name" x-model="mariee_name" placeholder="Sara"
                               class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2"
                               x-text="type === 'wedding' ? 'Nom du marié' : 'Nom du fiancé'"></label>
                        <input type="text" name="marie_name" x-model="marie_name" placeholder="Yassine"
                               class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                    </div>
                </div>

                <!-- Anniversary -->
                <div x-show="type === 'anniversary'" x-transition class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">Nom de la personne fêtée</label>
                        <input type="text" name="birthday_name" x-model="birthday_name" placeholder="Ibrahim"
                               class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">
                            Âge <span class="font-normal text-text-muted">(optionnel)</span>
                        </label>
                        <input type="number" name="birthday_age" x-model="birthday_age" min="0" max="120" placeholder="7"
                               class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                    </div>
                </div>

                <!-- Birth -->
                <div x-show="type === 'birth'" x-transition class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-text-secondary mb-2">Nom du bébé</label>
                            <input type="text" name="baby_name" x-model="baby_name" placeholder="Adam ou « À venir »"
                                   class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-text-secondary mb-2">Nom des parents</label>
                            <input type="text" name="parents_name" x-model="parents_name" placeholder="Famille Bennani"
                                   class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">Sexe</label>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="baby_gender = 'boy'"
                                    class="px-4 py-2 rounded-xl border-2 cursor-pointer text-sm font-medium transition-colors"
                                    :class="baby_gender === 'boy' ? 'border-primary bg-bg-high text-primary-soft' : 'border-bg-higher bg-bg-deep text-text-secondary hover:border-primary/40'">
                                Garçon
                            </button>
                            <button type="button" @click="baby_gender = 'girl'"
                                    class="px-4 py-2 rounded-xl border-2 cursor-pointer text-sm font-medium transition-colors"
                                    :class="baby_gender === 'girl' ? 'border-primary bg-bg-high text-primary-soft' : 'border-bg-higher bg-bg-deep text-text-secondary hover:border-primary/40'">
                                Fille
                            </button>
                            <button type="button" @click="baby_gender = 'surprise'"
                                    class="px-4 py-2 rounded-xl border-2 cursor-pointer text-sm font-medium transition-colors"
                                    :class="baby_gender === 'surprise' ? 'border-primary bg-bg-high text-primary-soft' : 'border-bg-higher bg-bg-deep text-text-secondary hover:border-primary/40'">
                                Surprise
                            </button>
                            <input type="hidden" name="baby_gender" :value="baby_gender">
                        </div>
                    </div>
                </div>

                <!-- Other -->
                <div x-show="type === 'other'" x-transition class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">Nom de l'événement</label>
                        <input type="text" name="event_name" x-model="event_name" placeholder="Soirée de gala"
                               class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">Description courte</label>
                        <input type="text" name="event_description" x-model="event_description" placeholder="Une soirée magique en famille"
                               class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                    </div>
                </div>

                <input type="hidden" name="type" :value="type">

                <div class="flex justify-end mt-8">
                    <button type="button" @click="nextStep()"
                            :disabled="!canProceedStep1()"
                            class="btn-primary"
                            :class="!canProceedStep1() ? 'opacity-40 cursor-not-allowed' : ''">
                        Suivant
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- ═══════ STEP 2 — Quand & Où ? ═══════ -->
        <div x-show="step === 2" x-transition.opacity>
            <div class="card">
                <h2 class="font-display font-bold text-2xl mb-2">Quand & où ?</h2>
                <p class="text-text-secondary text-sm mb-6">La date et le lieu de votre événement.</p>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">Quel jour ?</label>
                        <input type="date" name="event_date" x-model="event_date" min="<?= date('Y-m-d') ?>"
                               class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                    </div>

                    <div>
                        <label class="flex items-center gap-2 cursor-pointer text-sm font-medium text-text-primary mb-2">
                            <input type="checkbox" x-model="addTime" class="w-4 h-4 accent-primary">
                            Ajouter une heure précise
                        </label>
                        <input type="time" name="event_time" x-model="event_time" x-show="addTime" x-transition
                               class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">Ville</label>
                        <input type="text" name="city" x-model="city" placeholder="Casablanca, Marrakech, Tanger..."
                               class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-text-secondary mb-2">
                            Adresse précise <span class="font-normal text-text-muted">(optionnel)</span>
                        </label>
                        <input type="text" name="address" x-model="address" placeholder="Hôtel Hyatt Regency..."
                               class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                    </div>

                    <p class="text-sm text-text-muted flex items-start gap-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="text-primary-soft mt-0.5 flex-shrink-0">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        Vos invités auront un lien Google Maps direct.
                    </p>
                </div>

                <div class="flex justify-between mt-8 gap-3">
                    <button type="button" @click="prevStep()" class="btn-secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Précédent
                    </button>
                    <button type="button" @click="nextStep()"
                            :disabled="!canProceedStep2()"
                            class="btn-primary"
                            :class="!canProceedStep2() ? 'opacity-40 cursor-not-allowed' : ''">
                        Suivant
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- ═══════ STEP 3 — Le message ═══════ -->
        <div x-show="step === 3" x-transition.opacity>
            <div class="card">
                <h2 class="font-display font-bold text-2xl mb-2">Le message</h2>
                <p class="text-text-secondary text-sm mb-6">Le titre et le ton de votre invitation.</p>

                <!-- Title preview / edit -->
                <div class="mb-8 p-5 rounded-xl border border-bg-higher bg-bg-deep/40">
                    <div class="text-xs font-semibold uppercase tracking-widest text-text-muted mb-2">Titre de votre page</div>

                    <div x-show="!hasCustomTitle" class="flex items-center justify-between gap-3">
                        <span class="font-display font-extrabold text-xl md:text-2xl tracking-tight text-text-primary" x-text="generatedTitle()"></span>
                        <button type="button" @click="startEditTitle()" class="btn-ghost flex-shrink-0">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"/>
                            </svg>
                            Modifier
                        </button>
                    </div>

                    <div x-show="hasCustomTitle" class="flex flex-col sm:flex-row gap-2">
                        <input type="text" name="custom_title" x-model="custom_title" placeholder="Titre personnalisé"
                               class="flex-1 w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                        <button type="button" @click="resetTitle()" class="btn-secondary flex-shrink-0">Annuler</button>
                    </div>
                </div>

                <!-- Tone selection -->
                <div class="mb-6">
                    <div class="text-xs font-semibold uppercase tracking-widest text-text-muted mb-3">Ton de l'invitation</div>
                    <div class="space-y-3">
                        <button type="button" @click="selectTone('formal')"
                                class="w-full text-left rounded-xl p-4 flex gap-3 items-start transition-colors cursor-pointer border-2"
                                :class="tone === 'formal' ? 'border-primary bg-bg-high' : 'border-bg-higher bg-bg-raised hover:border-primary/40'">
                            <span class="text-2xl flex-shrink-0">🎩</span>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-text-primary mb-1">Formel</div>
                                <div class="text-sm text-text-secondary" x-text="messageFor('formal')"></div>
                            </div>
                        </button>

                        <button type="button" @click="selectTone('warm')"
                                class="w-full text-left rounded-xl p-4 flex gap-3 items-start transition-colors cursor-pointer border-2"
                                :class="tone === 'warm' ? 'border-primary bg-bg-high' : 'border-bg-higher bg-bg-raised hover:border-primary/40'">
                            <span class="text-2xl flex-shrink-0">💕</span>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-text-primary mb-1">Chaleureux</div>
                                <div class="text-sm text-text-secondary" x-text="messageFor('warm')"></div>
                            </div>
                        </button>

                        <button type="button" @click="selectTone('casual')"
                                class="w-full text-left rounded-xl p-4 flex gap-3 items-start transition-colors cursor-pointer border-2"
                                :class="tone === 'casual' ? 'border-primary bg-bg-high' : 'border-bg-higher bg-bg-raised hover:border-primary/40'">
                            <span class="text-2xl flex-shrink-0">🎉</span>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-text-primary mb-1">Décontracté</div>
                                <div class="text-sm text-text-secondary" x-text="messageFor('casual')"></div>
                            </div>
                        </button>
                    </div>
                </div>

                <input type="hidden" name="tone" :value="tone">

                <!-- Editable message -->
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">Personnalisez le message</label>
                    <textarea name="welcome_message" x-model="welcome_message" rows="5"
                              class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors resize-y leading-relaxed"></textarea>
                    <p class="text-xs text-text-muted mt-2">Vous pouvez modifier ce texte librement.</p>
                </div>

                <div class="flex justify-between mt-8 gap-3">
                    <button type="button" @click="prevStep()" class="btn-secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Précédent
                    </button>
                    <button type="button" @click="nextStep()" class="btn-primary">
                        Suivant
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- ═══════ STEP 4 — La cagnotte ═══════ -->
        <div x-show="step === 4" x-transition.opacity>
            <div class="card">
                <h2 class="font-display font-bold text-2xl mb-2">La cagnotte</h2>
                <p class="text-text-secondary text-sm mb-3">Recevez des cadeaux selon vos vrais besoins.</p>
                <div class="mb-6 inline-flex items-center gap-2 text-xs text-gold">
                    💰 Tout est optionnel sur cet écran.
                </div>

                <!-- Cagnotte type -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-text-secondary mb-3">Quel type de cagnotte ?</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" @click="cagnotte_type = 'travel'"
                                class="rounded-xl p-4 flex flex-col items-center gap-2 transition-colors cursor-pointer border-2"
                                :class="cagnotte_type === 'travel' ? 'border-primary bg-bg-high' : 'border-bg-higher bg-bg-raised hover:border-primary/40'">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" :class="cagnotte_type === 'travel' ? 'text-primary-soft' : 'text-text-secondary'">
                                <path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>
                            </svg>
                            <span class="text-sm font-semibold">Voyage</span>
                        </button>
                        <button type="button" @click="cagnotte_type = 'furniture'"
                                class="rounded-xl p-4 flex flex-col items-center gap-2 transition-colors cursor-pointer border-2"
                                :class="cagnotte_type === 'furniture' ? 'border-primary bg-bg-high' : 'border-bg-higher bg-bg-raised hover:border-primary/40'">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" :class="cagnotte_type === 'furniture' ? 'text-primary-soft' : 'text-text-secondary'">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                            <span class="text-sm font-semibold">Mobilier</span>
                        </button>
                        <button type="button" @click="cagnotte_type = 'free_gift'"
                                class="rounded-xl p-4 flex flex-col items-center gap-2 transition-colors cursor-pointer border-2"
                                :class="cagnotte_type === 'free_gift' ? 'border-primary bg-bg-high' : 'border-bg-higher bg-bg-raised hover:border-primary/40'">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" :class="cagnotte_type === 'free_gift' ? 'text-primary-soft' : 'text-text-secondary'">
                                <polyline points="20 12 20 22 4 22 4 12"/>
                                <rect x="2" y="7" width="20" height="5"/>
                                <line x1="12" y1="22" x2="12" y2="7"/>
                                <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
                                <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
                            </svg>
                            <span class="text-sm font-semibold">Cadeau libre</span>
                        </button>
                        <button type="button" @click="cagnotte_type = 'other'"
                                class="rounded-xl p-4 flex flex-col items-center gap-2 transition-colors cursor-pointer border-2"
                                :class="cagnotte_type === 'other' ? 'border-primary bg-bg-high' : 'border-bg-higher bg-bg-raised hover:border-primary/40'">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" :class="cagnotte_type === 'other' ? 'text-primary-soft' : 'text-text-secondary'">
                                <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/>
                            </svg>
                            <span class="text-sm font-semibold">Autre</span>
                        </button>
                    </div>
                </div>
                <input type="hidden" name="cagnotte_type" :value="cagnotte_type">

                <!-- Title -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-text-secondary mb-2">
                        Titre de la cagnotte <span class="font-normal text-text-muted">(optionnel)</span>
                    </label>
                    <input type="text" name="cagnotte_title" x-model="cagnotte_title" placeholder="Notre voyage de noces aux Maldives"
                           class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                </div>

                <!-- Amount -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-text-secondary mb-2">
                        Objectif <span class="font-normal text-text-muted">(en MAD, min 100, optionnel si « Autre »)</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="cagnotte_amount" x-model="cagnotte_amount" min="100" step="100" placeholder="50000"
                               class="w-full px-4 py-3 pr-16 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-text-muted text-sm font-semibold">MAD</span>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-text-secondary mb-2">Pourquoi cette cagnotte ?</label>
                    <textarea name="cagnotte_description" x-model="cagnotte_description" rows="3" placeholder="Quelques mots pour partager votre projet..."
                              class="w-full px-4 py-3 bg-bg-deep border border-bg-higher rounded-xl text-text-primary focus:border-primary focus:outline-none transition-colors resize-y leading-relaxed"></textarea>
                </div>

                <!-- Photo -->
                <div class="mb-5">
                    <label class="block text-sm font-medium text-text-secondary mb-2">
                        Photo <span class="font-normal text-text-muted">(optionnel, JPEG/PNG, max 5 MB)</span>
                    </label>
                    <input type="file" name="cagnotte_photo" accept="image/jpeg,image/png" @change="handlePhotoChange($event)"
                           class="block w-full text-sm text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-bg-higher file:text-text-primary file:font-semibold file:cursor-pointer hover:file:bg-bg-high cursor-pointer">

                    <div x-show="photoPreview" x-transition class="mt-3">
                        <img :src="photoPreview" alt="Aperçu" class="max-h-48 rounded-xl border border-bg-higher">
                    </div>
                    <p x-show="photoError" x-text="photoError" x-transition class="text-xs text-red-400 mt-2"></p>
                </div>

                <!-- Safety note -->
                <div class="p-4 rounded-xl bg-bg-deep/50 border border-bg-higher text-sm text-text-secondary mb-6 leading-relaxed">
                    <strong class="text-text-primary">ⓘ MyWish ne touche jamais l'argent.</strong> Les invités promettent un montant puis prouvent le paiement avec un virement direct vers vous.
                </div>

                <div class="flex justify-between gap-3">
                    <button type="button" @click="prevStep()" class="btn-secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Précédent
                    </button>
                    <button type="submit" class="btn-primary px-6 py-4 text-base font-bold">
                        Créer mon événement
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </button>
                </div>
            </div>
        </div>

    </form>

</section>

<script>
function wizard() {
    return {
        // ── State ──────────────────────────
        step: 1,
        type: '',
        mariee_name: '', marie_name: '',
        birthday_name: '', birthday_age: '',
        baby_name: '', parents_name: '', baby_gender: '',
        event_name: '', event_description: '',
        event_date: '', addTime: false, event_time: '', city: '', address: '',
        custom_title: '', hasCustomTitle: false,
        tone: 'warm', welcome_message: '',
        cagnotte_type: '', cagnotte_title: '', cagnotte_amount: '', cagnotte_description: '',
        cagnotte_photo: null, photoPreview: null, photoError: '',

        init() {
            this.$watch('step', (newStep) => {
                if (newStep === 3 && !this.welcome_message) {
                    this.welcome_message = this.messageFor(this.tone);
                }
            });
        },

        // ── Step labels for progress ───────
        stepLabel(s) {
            return ['Qui ?', 'Quand & où ?', 'Le message', 'La cagnotte'][s - 1] || '';
        },

        // ── Name accessors ─────────────────
        primaryName() {
            if (this.type === 'wedding' || this.type === 'engagement') return this.mariee_name;
            if (this.type === 'anniversary') return this.birthday_name;
            if (this.type === 'birth') return this.baby_name;
            if (this.type === 'other') return this.event_name;
            return '';
        },
        secondaryName() {
            if (this.type === 'wedding' || this.type === 'engagement') return this.marie_name;
            if (this.type === 'birth') return this.parents_name;
            return '';
        },

        // ── Validations ────────────────────
        namesValid() {
            if (this.type === 'wedding' || this.type === 'engagement') return !!(this.mariee_name && this.marie_name);
            if (this.type === 'anniversary') return !!this.birthday_name;
            if (this.type === 'birth') return !!(this.baby_name && this.parents_name);
            if (this.type === 'other') return !!this.event_name;
            return false;
        },
        canProceedStep1() {
            return this.type !== '' && this.namesValid();
        },
        canProceedStep2() {
            if (!this.event_date || !this.city) return false;
            const today = new Date().toISOString().slice(0, 10);
            return this.event_date >= today;
        },

        // ── French elision (mirrors event_types.php) ──
        elision(name) {
            if (!name) return 'de ';
            const first = name.charAt(0).toLowerCase();
            return 'aeiouyhàâéèêîïôû'.includes(first) ? "d'" : 'de ';
        },

        // ── Title preview (mirrors generateDefaultTitle in event_types.php) ──
        generatedTitle() {
            const p = this.primaryName();
            const s = this.secondaryName();
            const age = this.birthday_age ? parseInt(this.birthday_age) : null;
            switch (this.type) {
                case 'wedding':     return p && s ? `Le mariage de ${p} & ${s}` : 'Notre mariage';
                case 'engagement':  return p && s ? `Les fiançailles de ${p} & ${s}` : 'Nos fiançailles';
                case 'anniversary':
                    if (p && age) return `Les ${age} ans ${this.elision(p)}${p}`;
                    return p ? `L'anniversaire ${this.elision(p)}${p}` : 'Mon anniversaire';
                case 'birth':       return p ? `Bienvenue à ${p}` : 'Bienvenue au petit nouveau';
                case 'other':
                default:            return p || 'Mon événement';
            }
        },

        // ── Message preview per tone (mirrors generateWelcomeMessage) ──
        messageFor(t) {
            const fallbackP = (this.type === 'wedding' || this.type === 'engagement') ? 'Sara' : 'X';
            const fallbackS = (this.type === 'wedding' || this.type === 'engagement') ? 'Yassine' : '';
            const p = this.primaryName() || fallbackP;
            const s = this.secondaryName() || fallbackS;
            const age = this.birthday_age ? parseInt(this.birthday_age) : null;
            const agePhrase = age ? `les ${age} ans` : "l'anniversaire";
            const de = this.elision(p);

            const templates = {
                wedding: {
                    formal: `${p} & ${s} vous prient de leur faire l'honneur de votre présence à leur mariage.`,
                    warm:   `${p} & ${s} sont heureux de vous inviter à célébrer leur mariage en famille et entre amis.`,
                    casual: `On se marie ! ${p} & ${s}. Venez fêter ça avec nous, ça va être une journée inoubliable.`,
                },
                engagement: {
                    formal: `${p} & ${s} ont l'honneur de vous convier à la célébration de leurs fiançailles.`,
                    warm:   `${p} & ${s} sont heureux de vous inviter à célébrer leurs fiançailles.`,
                    casual: `${p} & ${s} se fiancent ! Venez célébrer cette belle étape avec nous.`,
                },
                anniversary: {
                    formal: `Vous êtes cordialement invité à célébrer ${agePhrase} ${de}${p}. Votre présence sera un cadeau précieux.`,
                    warm:   `Nous fêtons ${agePhrase} ${de}${p} ! Venez partager ce moment de joie en famille.`,
                    casual: `C'est l'anniversaire ${de}${p} ! Venez fêter ça avec nous.`,
                },
                birth: {
                    formal: `Nous avons l'immense bonheur de vous annoncer la naissance ${de}${p}. Vous êtes cordialement invité à célébrer cette joie.`,
                    warm:   `${p} est arrivé(e) parmi nous ! Toute la famille est aux anges. Venez partager ce moment magique.`,
                    casual: `Bienvenue à ${p} ! Venez faire connaissance avec le petit nouveau.`,
                },
                other: {
                    formal: `Vous êtes cordialement invité à cet événement spécial. Votre présence nous honorera.`,
                    warm:   `Nous serions ravis de partager ce moment avec vous. À très bientôt !`,
                    casual: `On organise un truc cool. Viens, ça va être sympa !`,
                },
            };
            return templates[this.type]?.[t] || templates.other[t] || '';
        },

        // ── Tone selection ─────────────────
        selectTone(t) {
            this.tone = t;
            this.welcome_message = this.messageFor(t);
        },

        // ── Title editing ──────────────────
        startEditTitle() {
            this.custom_title = this.generatedTitle();
            this.hasCustomTitle = true;
        },
        resetTitle() {
            this.custom_title = '';
            this.hasCustomTitle = false;
        },

        // ── Navigation ─────────────────────
        nextStep() {
            if (this.step === 1 && !this.canProceedStep1()) return;
            if (this.step === 2 && !this.canProceedStep2()) return;
            if (this.step < 4) {
                this.step++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },
        prevStep() {
            if (this.step > 1) {
                this.step--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        // ── Photo handling ─────────────────
        handlePhotoChange(e) {
            this.photoError = '';
            const file = e.target.files[0];
            if (!file) {
                this.cagnotte_photo = null;
                this.photoPreview = null;
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                this.photoError = 'Photo trop grosse (max 5 MB).';
                e.target.value = '';
                this.cagnotte_photo = null;
                this.photoPreview = null;
                return;
            }
            this.cagnotte_photo = file;
            this.photoPreview = URL.createObjectURL(file);
        },

        // ── Submit (final client-side checks) ──
        onSubmit(e) {
            if (this.cagnotte_type && this.cagnotte_type !== 'other') {
                const amt = parseFloat(this.cagnotte_amount);
                if (!amt || amt < 100) {
                    e.preventDefault();
                    alert('Le montant de la cagnotte doit être au moins 100 MAD (ou choisis « Autre »).');
                    return;
                }
            }
            if (this.cagnotte_photo && this.cagnotte_photo.size > 5 * 1024 * 1024) {
                e.preventDefault();
                alert('Photo trop grosse (max 5 MB).');
                return;
            }
            // Ensure welcome_message is populated even if user didn't visit step 3
            if (!this.welcome_message) {
                this.welcome_message = this.messageFor(this.tone);
            }
        },
    };
}
</script>
