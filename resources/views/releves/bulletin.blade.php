<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relevé de Notes - EPUMA</title>
    <style>
        @page {
            margin: 5mm 15mm;
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
            /* padding-bottom: 1rem; */
            text-align: center;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-table h2, .header-table h3, .header-table h4, .header-table p {
            margin: 2px 0;
        }
        .header-table h2 { font-size: 0.8em; }
        .header-table h3 { font-size: 0.6em; }
        .header-table h4 { font-size: 0.8em; font-style: italic;}
        .logo-placeholder {
            width: 50px;
            height: 50px;
            text-align: center;
            line-height: 50px;
            color: #999;
            font-size: 0.8em;
            margin: 0 auto;
        }
        .header-center-cell { width: 70%; }

        .header-center-cell p{
            font-size: 0.8em;
        }

        /** 📜 Le corps. */
        .main-title {
            text-align: center;
            font-size: 1.2em;
            font-weight: bold;
            text-decoration: underline;
            margin: 0.5rem 0 0.5rem 0;
        }
        /* 🆔 Section Identité, reconstruite avec une table */
        .student-photo {
            width: 21mm;
            height: 22mm;
            object-fit: cover;
            object-position: center center;
        }

        /** 👤 Les infos de l'étudiant, maintenant une table à 2 colonnes. */
        .student-info-table {
            font-size: 12px;
            width: 100%;
            margin-bottom: 1rem;
        }
        .student-info-table td {
            vertical-align: top;
            padding: 0 0.8rem;
        }
        .student-info-table td.student-photo-cell {
            padding: 0;
        }
        .student-info-table p {
            margin: 3px 0;
        }

        /** 📊 Le tableau des notes. Il était déjà une table, on peaufine. */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
            font-size: 9px;
        }
        .grades-table th, .grades-table td {
            border: 1px solid #333;
            padding: 5px 8px;
            text-align: left;
            vertical-align: middle;
        }
        .grades-table thead th {
            background-color: #f8f8f8;
            font-weight: bold;
            text-align: center;
        }
        .grades-table td.center-text, .grades-table th.center-text { text-align: center; }
        .grades-table tfoot td { 
            background-color: #f8f8f8; 
        }
        .ue-group-header th {
            background-color: #e9e9e9;
            font-style: italic;
            text-align: left;
            padding-left: 10px;
        }

        /** 🖋️ Le pied de page. */
        .transcript-footer { 
            font-size: 0.8em; 
        }
        .transcript-footer > table {
            width: 100%;
            align-items: flex-start;
        }
        .transcript-footer > table > tbody > tr > td:first-child {
            flex: 1;
            padding-right: 20px;
        }
        .transcript-footer > table > tbody > tr > td:last-child {
            flex: 0 0 auto;
            text-align: center;
            padding-left: 10px;
        }
        .grading-scale { font-size: 0.8em; text-align: center; margin-bottom: 0.8rem; }
        .semester-average { text-align: center; margin-bottom: 0; font-size: 1em; }
        .average-box { display: inline-block; border: 1px solid #333; padding: 5px 15px; margin: 0 10px; position: relative; bottom: -10px; }
        .decision { text-align: center; font-size: 1em; margin-bottom: 2rem; }
        .footer-contact { text-align: center; margin-top: 0; font-size: 0.8em; border-top: 1px solid #ccc; padding-top: 0.5rem; }

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
            margin-top: 1rem;
            font-style: italic;
            color: #555;
        }
        .director-signature { margin-top: 0; }
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
                <td style="width: 15%;"><div class="logo-placeholder">
                    <img src="{{ public_path('storage/logo-mesrs.png') }}" alt="Logo EPUMA" class="logo-epuma" style="max-width: 100%; height: auto; object-fit: cover;"/>
                </div></td>
                <td class="header-center-cell">
                    <h2>REPUBLIQUE DU BENIN</h2>
                    <p>Ministère de l'Enseignement Supérieur et de la Recherche Scientifique</p>
                    <h3>ECOLE POLYTECHNIQUE UNIVERSITAIRE DES METIERS D'AVENIR (EPUMA) LE PHENIX</h3>
                </td>
                <td style="width: 15%;"><div class="logo-placeholder">
                    <img src="{{ public_path('storage/logo-epuma.png') }}" alt="Logo EPUMA" class="logo-epuma" style="max-width: 100%; height: auto; object-fit: cover;"/>
                </div></td>
            </tr>
        </table>
        <table class="header-table">
            <tr>
                <td>
                    <p style="font-size: 9px; text-align:center"><strong>Arrêté</strong>: Nº2025-0762/MESRS/DC/SGM/DGES/DOSES/CJ/SA/020SGG25</p>
                </td>
            </tr>
        </table>
 
        <main class="transcript-body">
            <h1 class="main-title">RELEVÉ DE NOTES</h1>

            <!-- 👤 Infos de l'étudiant -->
            <table class="student-info-table">
                <tr>
                    <td class="student-photo-cell">
                        @if(isset($note['user']->profile) && $note['user']->profile && file_exists(public_path('storage/' . $note['user']->profile)))
                            <img src="{{ public_path('storage/' . $note['user']->profile) }}" alt="Photo de l'étudiant" class="student-photo"/>
                        @else
                            <img src="{{ public_path('storage/user_placeholder.jpg') }}" alt="Photo de l'étudiant" class="student-photo"/>
                        @endif
                    </td>
                    <td>
                        <p><strong>Nom :</strong> {{ $note['user']->lastname }}</p>
                        <p><strong>Prénoms :</strong> {{ $note['user']->firstname }}</p>
                        <p><strong>Date de Naissance :</strong> {{ $note['user']->birthdate }} à {{ $note['user']->birthplace }}</p>
                        <p><strong>Matricule :</strong> {{ $note['user']->matricule }}</p>
                    </td>
                    <td>
                        <p><strong>Cycle :</strong> {{ $cycle->name }}</p>
                        <p><strong>Filière :</strong> {{ $filiere->name }}</p>
                        <p><strong>Année :</strong> {{ $classe->year }} &nbsp;&nbsp;&nbsp; <strong>Semestre :</strong> {{ $year_part == 1 ? 'I' : 'II' }}</p>
                        <p><strong>Année universitaire :</strong> {{ $classe->academic_year }}-{{ $classe->academic_year + 1 }}</p>
                    </td>
                </tr>
            </table>

            <!-- 📊 Tableau des notes -->
            <table class="grades-table">
                <thead>
                    <tr>
                        <th style="width: 60%;">Code - UE</th>
                        <th style="width: 15%;">Crédits</th>
                        <th style="width: 15%;">Note /20</th>
                        <th style="width: 10%;">Côte</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $moyenne_generale = 0;
                        $total_total_coeffs = 0;

                        $calculateGrade = function($moyenne) {
                            if (is_null($moyenne)) return '';
                            if ($moyenne >= 16) return 'A';
                            if ($moyenne >= 14 && $moyenne < 16) return 'B';
                            if ($moyenne >= 12 && $moyenne < 14) return 'C';
                            if ($moyenne >= 10 && $moyenne < 12) return 'D';
                            return 'E';
                        };
                    @endphp

                    @foreach($unites as $unite)
                        @php
                            // 1. Calculs préliminaires pour cette UE
                            $total_coeffs = $unite->matieres->sum('coefficient');
                            
                            $somme_ponderee = 0;

                            foreach($unite->matieres as $m) {
                                $valeur_note = $note['notes'][$m->code] ?? 0;
                                
                                $somme_ponderee += $valeur_note;
                            }

                            
                            $moyenne_ue = $total_coeffs > 0 ? $somme_ponderee / $unite->matieres->count() : 0;

                            // Adding general informations
                            $moyenne_generale += $moyenne_ue * $total_coeffs;
                            $total_total_coeffs += $total_coeffs;

                        @endphp

                        <tr class="ue-group-header">
                            <th colspan="1">{{ $unite->code }} - {{ $unite->name }}</th>
                            
                            {{-- Somme des coefficients --}}
                            <th colspan="1" class="center-text">
                                {{ $total_coeffs }}
                            </th>

                            {{-- Moyenne pondérée --}}
                            <th colspan="1" class="center-text" style="{{ $moyenne_ue < 10 ? 'color: red;' : '' }}">
                                {{ number_format($moyenne_ue, 2, ',', '.') }}
                            </th>

                            <th colspan="1" class="center-text">{{ $calculateGrade($moyenne_ue) }}</th>
                        </tr>
                        @foreach($unite->matieres as $matiere)
                            <tr>
                                <td>{{ $matiere->code }} - {{ $matiere->name }}</td>
                                <td class="center-text">
                                    {{ $matiere->coefficient }}
                                </td>
                                <td class="center-text" style="{{ isset($note['notes'][$matiere->code]) && $note['notes'][$matiere->code] < 10 ? 'color: red;' : '' }}">
                                    @if(isset($note['notes'][$matiere->code]))
                                        {{ number_format($note['notes'][$matiere->code], 2, ',', '.') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="center-text">
                                    @if(isset($note['notes'][$matiere->code]))
                                        {{ $calculateGrade($note['notes'][$matiere->code])}}
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
                        <td colspan="1"><strong>Total</strong></td>
                        <td class="center-text"><strong>
                            {{ $total_total_coeffs ? $total_total_coeffs : 'N/A' }}
                        </strong></td>
                        <td></td>
                        <td colspan="1"></td>
                    </tr>
                </tfoot>
            </table>
        </main>

        @php
            // Calcul de la moyenne finale sécurisé
            $final_avg_raw = $total_total_coeffs > 0 ? ($moyenne_generale / $total_total_coeffs) : 0;
        @endphp
        <!-- 🖋️ Pied de page -->
        <footer class="transcript-footer">
            <table>
                <tr>
                    <td>
                        <p class="grading-scale">
                            Côte A > 16/20 Très-Bien - Côte B = entre 14 et 15,99 Bien - Côte C = entre 12 et 13,99 Assez-Bien<br>
                            Côte D = entre 10 et 11,99 - Côte E = moins de 10 Ajourné
                        </p>
                        <div class="semester-average">
                            <strong>MOYENNE DU SEMESTRE {{ $year_part }} /20:</strong><span class="average-box">{{ number_format($final_avg_raw, 2, ',', '.') }}</span>
                            <strong>Côte:</strong><span class="average-box">{{ $calculateGrade($final_avg_raw) }}</span>
                        </div>
                        <p class="decision"><strong>Décision du Conseil des Enseignants:</strong> 
                            @if($final_avg_raw >= 12)
                                ADMIS(E)
                            @elseif($final_avg_raw >= 10)
                                REPRISE
                            @else
                                AJOURNÉ(E)
                            @endif
                        </p>
                    </td>
                    <td>
                        <div style="border: 1px solid #ccc; padding: 2px; display: inline-block;">
                            <img src="{{ $note['qrCodePath'] }}" alt="Code QR de certification" style="width: 50px; height: 50px; opacity: 0.7;"/>
                        </div>
                    </td>
                </tr>
            </table>

            <table class="signatures-table">
                <tr>
                    <td>
                        <div class="signature-placeholder director-signature"></div>
                        <p><strong>M. ASSONGBA S. Anicet</strong><br>Directeur de EPUMA le Phénix</p>
                    </td>
                </tr>
            </table>
            <!-- <p class="footer-contact">Le Phénix/Ecole Polytechnique Universitaire des Métiers d'Avenir (EPUMA). Email: contact@epuma.lephenix.bj</p> -->
        </footer>
    </div>

</body>
</html>