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
            vertical-align: middle;
        }

        .header-table h2, .header-table h3, .header-table h4, .header-table p {
            margin: 2px 0;
        }

        .header-table h2 { font-size: 1.1em; }
        .header-table h3 { font-size: 1em; }
        .header-table h4 { font-size: 0.9em; font-style: italic; }

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

        .classe-info {
            text-align: center;
            margin: 20px 0;
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

        .students-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 9pt;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- En-tête -->
        <table class="header-table">
            <tr>
                <td width="20%">
                    <div class="logo-placeholder">Logo</div>
                </td>
                <td width="60%" style="text-align: center;">
                    <h2>ÉCOLE POLYTECHNIQUE UNIVERSITAIRE DE MPANDA</h2>
                    <h3>EPUMA</h3>
                    <h4>"Le savoir au service du développement"</h4>
                </td>
                <td width="20%">
                    &nbsp;
                </td>
            </tr>
        </table>

        <!-- Informations de la classe -->
        <div class="classe-info">
            <h3>LISTE DES ÉTUDIANTS</h3>
            <p><strong>Filière:</strong> {{ $filiere }}</p>
            <p><strong>Cycle:</strong> {{ $cycle }}</p>
            <p><strong>Année Académique:</strong> {{ $classe->academic_year }}</p>
        </div>

        <!-- Tableau des étudiants -->
        <table class="students-table">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Matricule</th>
                    <th>Nom</th>
                    <th>Prénoms</th>
                    <th>Sexe</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Titre</th>
                </tr>
            </thead>
            
        </table>

        <!-- Pied de page -->
        <div class="footer">
            <p>Secrétariat EPUMA<br>
            Document généré le {{ date('d/m/Y') }}</p>
        </div>
    </div>
</body>
</html>