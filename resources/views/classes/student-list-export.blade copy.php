<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Liste des Étudiants - EPUMA</title>
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

        .container {
            width: 100%;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 1rem;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header-table td {
            vertical-align: middle;e
        }
        
        .header-table h2, .header-table h3, .header-table h4, .header-table p {
            margin: 2px 0;
        }
        
        .header-table h2 { font-size: 1.1em; }
        .header-table h3 { font-size: 1em; }
        .header-table h4 { font-size: 0.9em; font-style: italic; }

        .class-info {
            margin: 20px 0;
            text-align: center;
        }

        .class-info p {
            margin: 5px 0;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .students-table th, .students-table td {
            border: 1px solid #333;
            padding: 6px 8px;
            font-size: 9pt;
        }

        .students-table th {
            background-color: #f8f8f8;
            font-weight: bold;
            text-align: center;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 10px 0;
            border-top: 1px solid #ccc;
            font-size: 9pt;
        }

        .page-number {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
        }

        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <table class="header-table">
            <tr>
                <td width="20%">
                    <div class="logo-placeholder">
                        LOGO
                    </div>
                </td>
                <td width="60%" style="text-align: center">
                    <h2>ÉCOLE PRIVÉE UNIVERSITAIRE DE MANAGEMENT APPLIQUÉ</h2>
                    <h3>EPUMA</h3>
                    <h4>Excellence - Rigueur - Travail</h4>
                    <p>BP: 16 162 Yaoundé - Tél: (+237) 222 31 02 96</p>
                </td>
                <td width="20%">
                    <div style="text-align: right">
                        <p>Année Académique</p>
                        <p><strong>{{ $classe->academic_year }}</strong></p>
                    </div>
                </td>
            </tr>
        </table>

        Classe : {{ var_dump($classe) }}
        Students : {{ var_dump($students) }}
        Cycle : {{ var_dump($cycle) }}
        Filiere : {{ var_dump($filiere) }}

        <!-- Informations de la classe -->
        <div class="class-info">
            <h2 style="text-decoration: underline; margin-bottom: 15px;">LISTE DES ÉTUDIANTS</h2>
            <p><strong>Filière:</strong> {{ $classe->filiere->name ?? 'Non définie' }}</p>
            <p><strong>Cycle:</strong> {{ $classe->cycle->name ?? 'Non défini' }}</p>
            <p><strong>Niveau:</strong> {{ $classe->year }}ème Année</p>
        </div>

        

        <!-- Tableau des étudiants -->
        <table class="students-table">
            <thead>
                <tr>
                    <th style="width: 5%">N°</th>
                    <th style="width: 15%">Nom</th>
                    <th style="width: 15%">Prénoms</th>
                    <th style="width: 12%">Matricule</th>
                    <th style="width: 8%">Sexe</th>
                    <th style="width: 20%">Email</th>
                    <th style="width: 12%">Téléphone</th>
                    <th style="width: 13%">Titre</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                <tr>
                    <td style="text-align: center">{{ $index + 1 }}</td>
                    <td>{{ $student->user->lastname }}</td>
                    <td>{{ $student->user->firstname }}</td>
                    <td style="text-align: center">{{ $student->user->matricule }}</td>
                    <td style="text-align: center">{{ $student->user->sexe }}</td>
                    <td>{{ $student->user->email }}</td>
                    <td>{{ $student->user->phone }}</td>
                    <td>{{ $student->titre }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pied de page -->
        <div class="footer">
            Sécrétariat EPUMA - Document généré le {{ date('d/m/Y') }}
        </div>

        <!-- Numéro de page -->
        <div class="page-number">
            Page 
        </div>
    </div>
</body>
</html>