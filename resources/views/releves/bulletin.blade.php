<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Bulletin de Notes - EPUMA</title>
    <style>
        @page {
            margin: 15mm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            color: #000;
            line-height: 1.4;
            font-size: 10pt;
            margin: 0;
        }

        /** 🏛️ Le conteneur principal. */
        .transcript-container {
            width: 100%;
        }

        /** 🔱 L'en-tête, reconstruit avec une table à 3 colonnes pour un contrôle total. */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 1rem;
            text-align: center;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-table h2, .header-table h3, .header-table h4, .header-table p {
            margin: 2px 0;
        }
        .header-table h2 { font-size: 1.1em; }
        .header-table h3 { font-size: 1em; }
        .header-table h4 { font-size: 0.9em; font-style: italic;}
        .logo-placeholder {
            border: 1px dashed #999;
            width: 80px;
            height: 80px;
            text-align: center;
            line-height: 80px;
            color: #999;
            font-size: 0.8em;
            margin: 0 auto;
        }
        .header-center-cell { width: 60%; }

        /** 📜 Le corps. */
        .main-title {
            text-align: center;
            font-size: 1.4em;
            font-weight: bold;
            text-decoration: underline;
            margin: 1.5rem 0;
        }

        /** 👤 Les infos de l'étudiant, maintenant une table à 2 colonnes. */
        .student-info-table {
            width: 100%;
            margin-bottom: 2rem;
        }
        .student-info-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 1rem;
        }
        .student-info-table p {
            margin: 3px 0;
        }

        /** 📊 Le tableau des notes. Il était déjà une table, on peaufine. */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        .grades-table th, .grades-table td {
            border: 1px solid #333;
            padding: 6px 8px;
            text-align: left;
            vertical-align: middle;
        }
        .grades-table thead th {
            background-color: #f8f8f8;
            font-weight: bold;
            text-align: center;
        }
        .grades-table td.center-text { text-align: center; }
        .grades-table tfoot td { background-color: #f8f8f8; }
        .ue-group-header th {
            background-color: #e9e9e9;
            font-style: italic;
            text-align: left;
            padding-left: 10px;
        }

        /** 🖋️ Le pied de page. */
        .transcript-footer { font-size: 0.9em; }
        .grading-scale { font-size: 0.8em; text-align: center; margin-bottom: 1.5rem; }
        .semester-average { text-align: center; margin-bottom: 1.5rem; font-size: 1.1em; }
        .average-box { display: inline-block; border: 1px solid #333; padding: 5px 15px; margin: 0 10px; }
        .decision { text-align: center; font-size: 1.1em; margin-bottom: 2rem; }
        .footer-contact { text-align: center; margin-top: 2rem; font-size: 0.8em; border-top: 1px solid #ccc; padding-top: 0.5rem; }

        /* Le chaos des signatures, dompté par une table. */
        .signatures-table {
            width: 100%;
            /* min-height: 150px; -- Ne fonctionne pas bien, on gère la hauteur avec le contenu */
        }
        .signatures-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: bottom;
            position: relative; /* Contexte pour les tampons */
        }
        .signature-placeholder {
            margin-top: 3rem;
            font-style: italic;
            color: #555;
        }
        .director-signature { margin-top: 1.5rem; }
        .certification-box {
            border: 2px solid #333;
            padding: 10px;
            margin: 0.5rem auto;
            width: 90%;
        }

        /* 🔥 Les tampons, positionnés par rapport à leur cellule parente. */
        /* NOTE: Les rotations (transform) sont très mal supportées. On les enlève pour la fiabilité. */
        /* NOTE: Les tampons seront des <img> dans la version finale. */
        .stamp {
            border: 2px solid;
            border-radius: 999px; /* Un grand nombre pour un cercle parfait */
            text-align: center;
            position: absolute;
            font-weight: bold;
            font-size: 0.8em;
            line-height: 1.2;
            padding-top: 25px; /* Simule un alignement vertical */
            box-sizing: border-box;
        }
        .municipal-stamp {
            border-color: #0000FF; color: #0000FF;
            width: 100px; height: 100px;
            bottom: 20px; left: -10px; /* Ajustement fin */
        }
        .red-stamp {
            border-color: #FF0000; color: #FF0000;
            width: 120px; height: 120px;
            bottom: 40px; left: 20px;
        }
        .director-stamp {
            border-color: #0000FF; color: #0000FF;
            width: 110px; height: 110px;
            top: -20px; right: -20px;
        }
    </style>
</head>
<body>

    <div class="transcript-container">
        
        <!-- 🔱 En-tête -->
        <table class="header-table">
            <tr>
                <td style="width: 20%;"><div class="logo-placeholder">
                    <img src="{{ public_path('storage/logo-mesrs.png') }}" alt="Logo EPUMA" class="logo-epuma" style="max-width: 100%; height: auto; object-fit: cover;"/>
                </div></td>
                <td class="header-center-cell">
                    <h2>REPUBLIQUE DU BENIN</h2>
                    <p>Ministère de l'Enseignement Supérieur et de la Recherche Scientifique</p>
                    <h3>ECOLE POLYTECHNIQUE UNIVERSITAIRE DES METIERS D'AVENIR (EPUMA)</h3>
                    <h4>Le Phénix</h4>
                </td>
                <td style="width: 20%;"><div class="logo-placeholder">
                    <img src="{{ public_path('storage/logo-epuma.png') }}" alt="Logo EPUMA" class="logo-epuma" style="max-width: 100%; height: auto; object-fit: cover;"/>
                </div></td>
            </tr>
        </table>

        <main class="transcript-body">
            <h1 class="main-title">BULLETIN DE NOTES</h1>

            <!-- 👤 Infos de l'étudiant -->
            <table class="student-info-table">
                <tr>
                    <td>
                        <p><strong>Cycle :</strong> {{ $cycle->name }}</p>
                        <p><strong>Filière :</strong> {{ $filiere->name }}</p>
                        <p><strong>Année :</strong> {{ $classe->year }}</p>
                        <p><strong>Année universitaire :</strong> {{ $classe->academic_year }}-{{ $classe->academic_year + 1 }}</p>
                        <p><strong>Semestre :</strong> {{ $year_part == 1 ? 'I' : 'II' }}</p>
                    </td>
                    <td>
                        <p><strong>Nom :</strong> {{ $note['user']->lastname }}</p>
                        <p><strong>Prénoms :</strong> {{ $note['user']->firstname }}</p>
                        <p><strong>Date de Naissance :</strong> {{ $note['user']->birthdate }}</p>
                        <p><strong>Lieu de Naissance :</strong> {{ $note['user']->birthplace }}</p>
                        <p><strong>N° matricule :</strong> {{ $note['user']->matricule }}</p>
                    </td>
                </tr>
            </table>

            <!-- 📊 Tableau des notes -->
            <table class="grades-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Unités d'Enseignement (UE)</th>
                        <th>Nombre de crédits</th>
                        <th>Note /100</th>
                        <th>Nombre de crédits validés</th>
                        <th>Pourcentage de crédit</th>
                        <th>Point</th>
                        <th>Côte</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $moyenne_generale = 0;
                        $total_total_coeffs = 0;
                    @endphp

                    @foreach($unites as $unite)
                        @php
                            // 1. Calculs préliminaires pour cette UE
                            $total_coeffs = $unite->matieres->sum('coefficient');
                            
                            $somme_ponderee = 0;
                            $credits_valides = 0;

                            foreach($unite->matieres as $m) {
                                $valeur_note = $note['notes'][$m->code] ?? 0;
                                
                                // $somme_ponderee += $valeur_note * $m->coefficient;
                                $somme_ponderee += $valeur_note;

                                if($valeur_note >= 10) {
                                    $credits_valides += $m->coefficient;
                                }
                            }

                            // $moyenne_ue = $total_coeffs > 0 ? $somme_ponderee / $total_coeffs : 0;
                            $moyenne_ue = $total_coeffs > 0 ? $somme_ponderee / $unite->matieres->count() : 0;
                            
                            $pourcentage = $total_coeffs > 0 ? ($credits_valides / $total_coeffs) * 100 : 0;

                            // Adding general informations
                            $moyenne_generale += $moyenne_ue * $total_coeffs;
                            $total_total_coeffs += $total_coeffs;

                        @endphp

                        <tr class="ue-group-header">
                            <th colspan="2">{{ $unite->code }}</th>
                            
                            {{-- Somme des coefficients --}}
                            <th colspan="1" style="text-align: center;">
                                {{ $total_coeffs }}
                            </th>

                            {{-- Moyenne pondérée --}}
                            <th colspan="1" style="text-align: center;">
                                {{ number_format($moyenne_ue, 2) }}
                            </th>

                            {{-- Crédits validés --}}
                            <th colspan="1" style="text-align: center;">
                                {{ $credits_valides }}
                            </th>

                            {{-- Pourcentage --}}
                            <th colspan="1" style="text-align: center;">
                                {{ number_format($pourcentage, 2) }}%
                            </th>

                            <th colspan="5"></th>
                        </tr>

                        @foreach($unite->matieres as $matiere)
                            <tr>
                                <td>{{ $matiere->code }}</td>
                                <td>{{ $matiere->name }}</td>
                                <td class="center-text">
                                    {{ $matiere->coefficient }}
                                </td>
                                <td class="center-text">
                                    @if(isset($note['notes'][$matiere->code]))
                                        {{ number_format($note['notes'][$matiere->code], 2) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="center-text">
                                    {{ $note['notes'][$matiere->code] >= 10 ? $matiere->coefficient : 0 }}
                                </td>
                                <td class="center-text">
                                    @if(isset($note['count_validated']))
                                        {{ number_format(($note['count_validated'] / ($note['count_validated'] + $note['count_non_validated'])) * 100, 0) }}%
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="center-text">
                                    @if(isset($note['points'][$matiere->code]))
                                        {{ number_format($note['count_validated'] * $matiere->coefficient, 2) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="center-text">
                                    @if(isset($note['cote']))
                                        {{ $note['cote'] }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"><strong>Total</strong></td>
                        <td class="center-text"><strong>
                            {{ $somme_coeffs ? $somme_coeffs : 'N/A' }}
                        </strong></td>
                        <td></td>
                        <td class="center-text"><strong>{{ $totalValidatedCoeff ? $totalValidatedCoeff : 'N/A' }}</strong></td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>
        </main>

        @php
            // Utilisation d'une fonction anonyme pour éviter les conflits de nom de fonction globale
            $calculateGrade = function($moyenne) {
                if (is_null($moyenne)) return '';
                if ($moyenne >= 90) return 'A';
                if ($moyenne >= 80 && $moyenne < 90) return 'B';
                if ($moyenne >= 70 && $moyenne < 80) return 'C';
                if ($moyenne >= 60 && $moyenne < 70) return 'D';
                if ($moyenne >= 50 && $moyenne < 60) return 'E';
                return 'F';
            };

            // Calcul de la moyenne finale sécurisé
            // La logique (x5) est conservée selon votre code original
            $final_avg_raw = $total_total_coeffs > 0 ? ($moyenne_generale / $total_total_coeffs) * 5 : 0;
        @endphp
        <!-- 🖋️ Pied de page -->
        <footer class="transcript-footer">
            <p class="grading-scale">
                Côte A > 90/100 Excellent - Côte B = entre 80 et 90 Tbien - Côte C = entre 70 et 80 Bien<br>
                Côte D = entre 60 et 70 Abien - Côte E = entre 50 et 60 Passable Côte F = ajourné
            </p>
            <div class="semester-average">
                <strong>MOYENNE DU SEMESTRE {{ $year_part }} /100:</strong><span class="average-box">{{ number_format($final_avg_raw, 2) }}</span>
                <strong>Côte:</strong><span class="average-box">{{ $calculateGrade($final_avg_raw) }}</span>
            </div>
            <p class="decision"><strong>Décision du Conseil des Enseignants:</strong> {{ $final_avg_raw >= 10 ? 'Admis(e)' : 'Non admis(e)' }}</p>

            <table class="signatures-table">
                <tr>
                    <td>
                        <div class="signature-placeholder director-signature">Signature ASSONGBA S. Anicet</div>
                        <p><strong>M. ASSONGBA S. Anicet</strong><br>Directeur de EPUMA le Phénix</p>
                    </td>
                </tr>
            </table>

            <p class="footer-contact">Le Phénix/Ecole Polytechnique Universitaire des Métiers d'Avenir (EPUMA). Email: contact@epuma.lephenix.bj</p>
        </footer>
    </div>

</body>
</html>