<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fiche de Pré-Inscription - Le Phénix</title>
    <style>
        @page {
            /* On définit les marges PHYSIQUES du document. 10mm tout autour. */
            margin: 10mm;
        }
        /* 📜 Styles généraux que même un moteur archaïque peut comprendre */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif; /* Utilisation de polices de base, plus fiables */
            font-size: 10pt;
            color: #000;
            background-color: #fff; /* Le PDF n'a pas besoin de fond de body */
            margin: 0;
        }

        /* 📄 Le conteneur de page. Pas de fioritures. */
        .page-container {
            width: 100%;
        }

        /* 🔥 Le filigrane. On utilise une div en position fixe derrière le contenu. C'est plus robuste. */
        #watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -40%);
            z-index: -1000;
            opacity: 0.1;
        }

        /* 🔱 En-tête avec une table pour l'alignement */
        .page-header h1 { font-size: 1.2em; margin: 0; }
        .page-header h2 { font-size: 1.5em; font-family: 'Helvetica', 'Arial Black', sans-serif; margin: 5px 0; }
        .page-header p { font-size: 0.8em; margin: 0; }
        .logo-epuma { width: 100px; }

        /* 🆔 Section Identité, reconstruite avec une table */
        .student-photo {
            width: 35mm;
            height: 45mm;
            border: 1px solid #000;
            object-fit: cover;
            object-position: center center;
        }

        /* ✍️ Style des "champs de formulaire" */
        .form-group-label {
            width: 100px;
            border: 1px solid #000;
            padding: 4px;
            font-size: 0.9em;
            text-align: center;
            background-color: #f0f0f0; /* Léger fond pour les labels */
        }
        .value-box {
            border: 1px solid #000;
            border-left: none; /* Pour fusionner les bordures */
            padding: 4px 8px;
            font-weight: bold;
            width: 100%;
        }

        .section-title {
            text-align: center;
            font-size: 1.1em;
            font-family: 'Helvetica', 'Arial Black', sans-serif;
            border: 2px solid #000;
            background: #e0e0e0;
            padding: 3px;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        /* ✍️ Grille d'inscription, maintenant une table solide */
        .inscription-table {
            width: 100%;
            border-collapse: collapse; /* Fusion des bordures */
        }
        .inscription-table td, .inscription-table th {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }
        .inscription-table th {
            background-color: #f0f0f0;
        }
        .inscription-table .value-box-grid {
            font-weight: bold;
        }


        /* ⚠️ Texte d'avertissement */
        .warning-text {
            color: #c00;
            font-size: 0.9em;
            font-weight: bold;
            text-align: center;
            margin: 1.5rem 0;
        }

        /* 📋 Procédures, maintenant une table à 2 colonnes */
        .procedures-table {
            width: 100%;
            border: 2px solid #000;
            border-collapse: collapse;
        }
        .procedures-table td {
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
        .procedures-table .column-divider {
            border-left: 2px solid #000;
        }

        .column-title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin: 0 0 10px 0;
        }
        .procedures-table ol,
        .procedures-table ul {
            padding-left: 20px;
            margin: 0;
            font-size: 0.85em;
            line-height: 1.5;
        }

        /* 📚 Pied de page */
        .page-footer { margin-top: 1.5rem; }
        .library-info { border: 2px solid #000; padding: 10px; }
        .final-note { margin-top: 1rem; font-size: 0.9em; }

    </style>
</head>
<body>
    <!-- 🔥 Le filigrane. Plus fiable en tant que div dédiée. -->
    <div id="watermark">
        <img src="{{ public_path('storage/logo-epuma.png') }}" width="400" alt=""/>
    </div>

    <div class="page-container">
        <!-- 🔱 En-tête du document (Tableau 1) -->
        <table class="page-header" style="width: 100%; margin-bottom: 1rem;">
            <tr>
                <td style="vertical-align: top;">
                    <h1>EPUMA Le Phénix</h1>
                    <h2>Fiche de Pré-Inscription</h2>
                    <p><strong>Compte</strong> Coris Bank CPET LE PHENIX N° 002420224101</p>
                    <!-- <p><strong>Arrêté de création</strong>: Nº2025-0758/MESRS/DC/SGM/DGES/DOSES/CJ/SA/020SGG25</p> -->
                    <p><strong>Arrêté</strong>: Nº2025-0762/MESRS/DC/SGM/DGES/DOSES/CJ/SA/020SGG25</p>
                </td>
                <td style="vertical-align: top; text-align: right;">
                    <img src="{{ public_path('storage/logo-epuma.png') }}" alt="Logo EPUMA" class="logo-epuma"/>
                </td>
            </tr>
        </table>

        <!-- 🆔 Section Identité (Tableau 2) -->
        <table id="identity-section" style="width: 100%;">
            <tr>
                <!-- Colonne Photo -->
                <td style="width: 110px; vertical-align: top;">
                    @if(isset($user->profile) && $user->profile && file_exists(public_path('storage/' . $user->profile)))
                        <img src="{{ public_path('storage/' . $user->profile) }}" alt="Photo de l'étudiant" class="student-photo"/>
                    @else
                        <img src="{{ public_path('storage/user_placeholder.jpg') }}" alt="Photo de l'étudiant" class="student-photo"/>
                    @endif
                    @if(isset($qrCodeDataUri) && $qrCodeDataUri)
                        <div style="position: relative; top: -3mm; display: table; margin: 0 auto;">
                            <img src="{{ $qrCodeDataUri }}" alt="QR Code RR" style="width: 50pt; height: 50pt; opacity: 0.5;" />
                        </div>
                    @endif
                </td>
                <!-- Colonne Infos -->
                <td style="padding-left: 20px; vertical-align: top;">
                    <div style="text-align: right; border: 1px solid #000; padding: 4px 8px; font-weight: bold; margin-bottom: 5px;">
                        2#10459619#20#LPs3s4-SECINF
                    </div>
                    <div style="text-align: right; font-weight: bold; margin-bottom: 1rem;">
                        Année Académique :
                        <span style="border: 1px solid #000; padding: 2px 20px; margin-left: 5px;">
                            {{ $classe->academic_year }} - {{ $classe->academic_year + 1 }}
                        </span>
                    </div>

                    <h3 class="section-title">IDENTITE</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td class="form-group-label">Identifiant :</td>
                            <td class="value-box">{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td class="form-group-label">Matricule :</td>
                            <td class="value-box">{{ $student->matricule ?? 'Non défini' }}</td>
                        </tr>
                        <tr>
                            <td class="form-group-label">Nom :</td>
                            <td class="value-box">{{ $user->lastname }}</td>
                        </tr>
                        <tr>
                            <td class="form-group-label">Prénoms :</td>
                            <td class="value-box">{{ $user->firstname }}</td>
                        </tr>
                        <tr>
                            <td class="form-group-label">Né le :</td>
                            <td class="value-box" style="border-right: none;">
                                {{-- Formatage de date. Ex: \Carbon\Carbon::parse($user->birthdate)->format('d/m/Y') --}}
                                {{ $user->birthdate ?? 'Non défini' }}
                                <span style="font-weight: normal; margin: 0 10px;">A :</span>
                                {{ $user->birthplace ?? 'Non défini' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="form-group-label">Nationalité :</td>
                            <td class="value-box">{{ $user->nationality ?? 'Béninoise' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- ✍️ Section Inscription -->
        <section id="inscription-section" style="margin-top: 1rem;">
            <h3 class="section-title">Inscription</h3>
            <table class="inscription-table">
                <thead>
                    <tr>
                        <th>Cycle :</th>
                        <th>Filière :</th>
                        <th>Année :</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="value-box-grid">{{ $cycle->name }}</td>
                        <td class="value-box-grid">{{ $filiere->name }}</td>
                        <td class="value-box-grid">{{ $classe->year }}</td>
                    </tr>
                </tbody>
                <thead>
                    <tr>
                        <th>Statut :</th>
                        <th>Montant :</th>
                        <th>Restant dû :</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="value-box-grid">{{ $student->titre }}</td>
                        {{-- Utilise un helper pour formater les montants --}}
                        <td class="value-box-grid">
                            {{-- Calcul de la scolarité ajustée selon le type de tag --}}
                            @if($tag->type === 'amount')
                                {{ number_format(($classe->fee - $tag->fee) ?? 415000, 2, ',', ' ') }} FCFA
                            @elseif($tag->type === 'percentage')
                                {{ number_format(($classe->fee - ($classe->fee * $tag->fee / 100)) ?? 415000, 2, ',', ' ') }} FCFA
                            @else
                                {{ number_format($tag->fee ?? 415000, 2, ',', ' ') }} FCFA
                            @endif
                        </td>
                        <td class="value-box-grid">{{ number_format($student->due_amount ?? 0, 2, ',', ' ') }} FCFA</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <!-- ⚠️ Avertissement important -->
        <p class="warning-text">
            <strong>Attention:</strong> Veuillez vérifier l'exactitude des données présentes sur cette fiche avant tout paiement. Aucun remboursement ni report ne sera possible après paiement.
        </p>

        <!-- 📋 Procédures et pièces à fournir. -->
        <table class="procedures-table">
            <tr>
                <td>
                    <h4 class="column-title">Les étapes de la procédure d'inscription</h4>
                    <ol>
                        <li>Obtention de l'autorisation d'inscription...</li>
                        <li>Retrait de la Fiche de préinscription...</li>
                        <li>Prise de photo numérique...</li>
                        <li>Versement à la banque...</li>
                        <li>Validation du dossier d'inscription...</li>
                    </ol>
                </td>
                <td class="column-divider">
                     <h4 class="column-title">Pièces à fournir pour s'inscrire à l'EPUMA</h4>
                    <ul>
                        <li>Fiche de préinscription + sa photocopie.</li>
                        <li>Copie du relevé du BAC...</li>
                        <li>Photocopie de la carte d'identité...</li>
                        <li>Copie légalisée de l'acte de naissance...</li>
                        <li>Originale + une photocopie de la quittance...</li>
                        <li>L'ancienne carte d'étudiant...</li>
                        <li>Certificat de nationalité...</li>
                        <li>Copie légalisée du diplôme...</li>
                    </ul>
                </td>
            </tr>
        </table>

        <!-- 📚 Pied de page -->
        <footer class="page-footer">
            <!-- <div class="library-info">
                <h4 class="column-title">Bibliothèque Universitaire</h4>
                <p>La Bibliothèque de l'Ecole Polytechnique Universitaire des Métiers d'Avenir...</p>
            </div> -->
            <p class="final-note">
                <strong>NB:</strong> La validation de votre inscription est obligatoire...
            </p>
        </footer>
    </div>
</body>
</html>