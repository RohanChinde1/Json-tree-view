<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Task-3</title>
 <link rel="icon" type="image/x-icon" href="images/download.jpg">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
<style>
  body {
            background-color: lightblue; 
        }
.container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

#identifierDropdown {
  margin-bottom: 20px;
}

#dataDisplay {
  width: 80%;
  margin-top: 20px;
}
</style>
</head>
<body>

<div class="container">
  <h1>JSON Tree View</h1>

  <div>
    <select id="identifierDropdown">
      <option value="">Select Identifier</option>
      <?php
      // Path to XML file
      $xmlFilePath = 'C:\\Users\\rohan\\Downloads\\tenementXmlExtracts_2023-12-15_08-09-55-0\\tenementXmlExtracts_2023-12-15_08-09-55-0.xml';

      // Read XML file
      $xml = file_get_contents($xmlFilePath);

      if ($xml === false) {
          die('Failed to read XML file.');
      }

      // Convert XML to JSON
      $doc = new DOMDocument();
      $doc->preserveWhiteSpace = false; // Disable whitespace preservation to avoid formatting issues
      if ($doc->loadXML($xml) === false) {
          die('Failed to load XML.');
      } 

      $json = json_encode(simplexml_import_dom($doc), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

      if ($json === false) {
          die('Failed to convert XML to JSON.');
      }

      // Decode JSON data
      $jsonData = json_decode($json);

      // Iterate through each tenement entry and create an option for the dropdown menu
      foreach ($jsonData->tenement as $entry) {
          echo '<option value="' . htmlspecialchars($entry->identifier) . '">' . $entry->identifier . '</option>';
      }
      ?>
    </select>
  </div>

  <div id="dataDisplay">
    <!-- Data will be displayed here -->
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var dropdown = document.getElementById('identifierDropdown');
  var dataDisplay = document.getElementById('dataDisplay');
  var jstreeInstance = null; // To store the jstree instance

  //  event listener to dropdown
  dropdown.addEventListener('change', function() {
    var selectedIdentifier = dropdown.value;

    // If no identifier selected, clear the data display and destroy the jstree instance
    if (!selectedIdentifier) {
      clearDataDisplay();
      return;
    }

    // Find the corresponding data in the JSON object
    var jsonData = <?php echo $json; ?>;
    var selectedData = jsonData.tenement.find(function(entry) {
      return entry.identifier === selectedIdentifier;
    });

    // Display the data
    if (selectedData) {
      // Clear previous data and destroy the existing jstree instance
      clearDataDisplay();

      // Convert JSON data into jsTree format
      var treeData = convertToTree(selectedData);

      // Initialize jsTree with the data
      jstreeInstance = $(dataDisplay).jstree({
        'core': {
          'data': treeData
        }
      });
    } else {
      dataDisplay.innerHTML = 'Data not found for the selected identifier.';
    }
  });

  // Function to clear the data display and destroy the jstree instance
  function clearDataDisplay() {
    dataDisplay.innerHTML = '';
    if (jstreeInstance) {
      $(dataDisplay).jstree(true).destroy();
      jstreeInstance = null;
    }
  }

  // Function to convert JSON data into jsTree format
  function convertToTree(data) {
    var treeData = [];

    for (var key in data) {
      if (data.hasOwnProperty(key)) {
        var node = {
          'text': key,
          'children': []
        };

        if (typeof data[key] === 'object') {
          node.children = convertToTree(data[key]);
        } else {
          node.text += ': ' + data[key];
        }

        treeData.push(node);
      }
    }

    return treeData;
  }
});
</script>

</body>
</html>