
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <link rel="stylesheet" href="../css/style.css" media="screen" type="text/css" />
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;500;600;700&display=swap" rel="stylesheet">
        <title>Volley Manager</title>
    </head>
    <body>

        <?php include 'header.php'; ?>

        <div id="containerTable">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <tr cclass="collapsible" onclick="toggleRow(this)">
                        <td>1</td>
                        <td>John Smith</td>
                    </tr>
                    <tr class="hidden hiddenStill">
                        <td colspan="2">More details about John Smith...</td>
                    </tr>
                    <tr class="foldable" onclick="toggleRow(this)">
                        <td>2</td>
                        <td>Alice Brown</td>
                    </tr>
                    <tr class="hidden hiddenStill">
                        <td colspan="2">
                            <div>
                                <p>More details about Alice Brown...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    <script>
        function toggleRow(row) {
            const nextRow = row.nextElementSibling;

            if (nextRow && nextRow.classList.contains('hidden')) {
                nextRow.classList.remove('hidden');
            } else if (nextRow) {
                nextRow.classList.add('hidden');
            }
        }
    </script>

    </body>
</html>