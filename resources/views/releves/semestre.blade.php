<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relevé de Notes de Classe</title>
    <style>
        /* 📜 On commande la page pour qu'elle soit en paysage par défaut */
        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #000;
            font-size: 9pt;
            margin: 0;
        }

        /** 📄 Le conteneur principal. Il occupe tout l'espace défini par @page. */
        .page {
            width: 100%;
            height: 100%;
        }

        /* 🔱 En-tête du document, reconstruit avec une table pour un alignement parfait */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 0.5rem;
        }
        .header-table h3, .header-table h4, .header-table p { margin: 0; }
        .header-table h3 { font-size: 1.1em; }
        .header-table h4 { font-size: 1em; font-weight: normal; }
        .header-table p { font-size: 0.9em; }
        .header-right-cell { text-align: right; vertical-align: top; }
        
        .main-title {
            text-align: center;
            font-size: 1.5em;
            margin: 1rem 0;
        }

        /** 📊 Le tableau des notes. Le cœur de la création. */
        .class-grades-table {
            width: 100%;
            border-collapse: collapse;
        }
        .class-grades-table th, .class-grades-table td {
            border: 1px solid #ccc;
            padding: 4px 6px;
            text-align: center;
            vertical-align: middle;
        }

        /* 🔥 Style des en-têtes complexes. */
        .class-grades-table thead th {
            background-color: #e9ecef;
            font-weight: bold;
            font-size: 0.9em;
            padding: 8px 4px;
        }
        .ue-header-row th {
            border-bottom: 2px solid #555;
        }
        .subject-header-row th {
            font-weight: normal;
            font-size: 0.8em;
        }

        /* 👤 Style des colonnes spécifiques. */
        .student-name-col {
            width: 220px; /* On garde cette largeur pour guider le rendu */
            text-align: left;
            padding-left: 8px;
        }

        /* 🦓 Les rayures zébrées. On ne peut pas utiliser nth-child, on le fera dans Blade. */
        .zebra-stripe {
            background-color: #f8f9fa;
        }

        /* 📈 Mise en évidence des colonnes de résumé. */
        .summary-col {
            background-color: #f1f3f5;
            font-weight: bold;
        }
        .student-average {
            font-size: 1.1em;
        }

        /* 🎨 Les codes couleur pour les côtes. */
        .grade-a { color: #28a745; }
        .grade-b { color: #17a2b8; }
        .grade-d { color: #fd7e14; }
        /* NOTE: Si la couleur ne s'affiche pas, le moteur PDF est peut-être configuré pour ignorer les couleurs. */

        /* 📉 La ligne des moyennes de classe. */
        .class-average-row td {
            background-color: #e9ecef;
            font-weight: bold;
            font-size: 1em;
            border-top: 2px solid #555;
        }

        /** 🖋️ Le pied de page, également reconstruit avec une table. */
        .footer-table {
            width: 100%;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #000;
        }
        .footer-date-cell { text-align: left; vertical-align: bottom; }
        .footer-signature-cell { text-align: center; vertical-align: bottom; }
    </style>
</head>
<body>

    <div class="page landscape">

        <!-- 🔱 En-tête du document -->
        <table class="header-table">
            <tr>
                <td style="width: 40px;">
                    <div class="logo-placeholder">
                        <img src="{{ public_path('storage/logo-epuma.png') }}" alt="Logo EPUMA" class="logo-epuma" style="max-width: 100%; height: auto; object-fit: cover;"/>
                    </div>
                </td>
                <td>
                    <h3>ECOLE POLYTECHNIQUE UNIVERSITAIRE DES METIERS D'AVENIR (EPUMA) Le Phénix</h3>
                    <h4>{{ $cycle->name }} - {{ $filiere->name }}</h4>
                </td>
                <td class="header-right-cell">
                    <p><strong>Année universitaire :</strong> {{ $classe->academic_year }}-{{ $classe->academic_year + 1 }}</p>
                    <p><strong>Semestre :</strong> Semestre {{ $year_part == 1 ? 'I' : 'II' }}</p>
                </td>
            </tr>
        </table>
        
        <h1 class="main-title">RÉSULTATS DE FIN DE SEMESTRE</h1>

        <main class="class-grades-container">
            <table class="class-grades-table">
                <thead>
                    <tr class="ue-header-row">
                        <th rowspan="2">N°</th>
                        <th rowspan="2" class="student-name-col">Noms et Prénoms</th>
                        @foreach($unites as $unite)
                            <th colspan="{{ $unite->matieres->count() }}">{{ $unite->name }}</th>
                        @endforeach
                        <th rowspan="2" class="summary-col">Moy. Gén.</th>
                        <th rowspan="2" class="summary-col">Côte</th>
                    </tr>
                    <tr class="subject-header-row">
                        @foreach($unites as $unite)
                            @foreach($unite->matieres as $matiere)
                                <th>{{ $matiere->code }}<br>({{ $matiere->coefficient }} crédits)</th>
                            @endforeach
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach ($notes as $note)
                        <tr class="{{ $loop->even ? 'zebra-stripe' : '' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td class="student-name-col">{{ $note['name'] }}</td>
                            @foreach($unites as $unite)
                                @foreach($unite->matieres as $matiere)
                                    <td>
                                        @if(isset($note['notes'][$matiere->code]))
                                            {{ number_format($note['notes'][$matiere->code], 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                @endforeach
                            @endforeach
                            <td class="summary-col student-average">
                                @if(isset($note['moyenne']))
                                    {{ number_format($note['moyenne'], 2) }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="summary-col {{ $note['cote'] ?? 'grade-d' }}">
                                @if(isset($note['cote']))
                                    {{ $note['cote'] }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <!-- <tfoot>
                    <tr class="class-average-row">
                        <td colspan="2"><strong>MOYENNE DE LA CLASSE</strong></td>
                        @foreach($unites as $unite)
                            @foreach($unite->matieres as $matiere)
                                <td>
                                    <strong>
                                        @if(isset($meansPerMatiere[$matiere->code]))
                                            {{ number_format($meansPerMatiere[$matiere->code] ?? 0, 2) }}
                                        @else
                                            N/A
                                        @endif
                                    </strong>
                                </td>
                            @endforeach
                        @endforeach
                        <td colspan="2"></td>
                    </tr>
                </tfoot> -->
            </table>
        </main>
        
        <!-- 🖋️ Pied de page -->
        <table class="footer-table">
            <tr>
                <td class="footer-date-cell">Fait à Porto-Novo, le {{ now()->format('d') }} / {{ now()->format('m') }} / {{ now()->format('Y') }}</td>
                <td class="footer-signature-cell">
                    <strong>Le Directeur de l'EPUMA le Phénix</strong><br><br><br>
                    <span>M. ASSONGBA S. Anicet</span>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>