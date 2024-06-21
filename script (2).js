
    var names = []; // Holds all added names
    var nameElements = []; // Tracks DOM elements for names
    var selectedIndices = new Set(); // Tracks selected names' indices
    const nameList = document.getElementById('nameList'); // Display area for names
    let lastPickedIndex = null; // Track the last picked name's index for color reset

    document.getElementById('addName').addEventListener('click', () => {
        const name = document.getElementById('nameInput').value.trim();
        addName(name);
    });

    document.getElementById('nameInput').addEventListener('keypress', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent the default form submission
            const name = event.target.value.trim();
            addName(name);
        }
    });

    document.getElementById('addNamesFromExcel').addEventListener('click', () => {
        const namesFromInput = document.getElementById('namesInput').value.split('\n').map(name => name.trim());
        namesFromInput.forEach(name => {
            if (name) addName(name);
        });
    });

    document.getElementById('selectAllNames').addEventListener('click', () => {
        nameElements.forEach((element, index) => {
            if (!selectedIndices.has(index)) {
                toggleSelection(element, index);
            }
        });
    });

    document.getElementById('pickName').addEventListener('click', () => {
        if (selectedIndices.size > 0) {
            const indicesArray = Array.from(selectedIndices);
            const randomIndex = indicesArray[Math.floor(Math.random() * indicesArray.length)];
            highlightPickedName(randomIndex);
        } else {
            alert('Please select at least one name.');
        }
    });

    function addName(name) {
        if (name && !names.includes(name)) {
            names.push(name);
            const div = createNameItem(name);
            nameList.appendChild(div);
            document.getElementById('nameInput').value = ''; // Clear input
            document.getElementById('namesInput').value = ''; // Clear textarea for bulk input
        }
    }

    function createNameItem(name) {
        const div = document.createElement('div');
        div.textContent = name;
        div.className = 'name-item';
        div.addEventListener('click', function() {
            const index = names.indexOf(name);
            toggleSelection(this, index);
        });
        nameElements.push(div);
        return div;
    }

    function toggleSelection(element, index) {
        if (selectedIndices.has(index)) {
            selectedIndices.delete(index);
            element.style.backgroundColor = ''; // Remove selection color
        } else {
            selectedIndices.add(index);
            element.style.backgroundColor = 'yellow'; // Highlight selection
        }
    }

    function highlightPickedName(index) {
        if (lastPickedIndex !== null && nameElements[lastPickedIndex]) {
            nameElements[lastPickedIndex].style.backgroundColor = selectedIndices.has(lastPickedIndex) ? 'yellow' : '';
        }
        const element = nameElements[index];
        document.getElementById('result').textContent = `Selected Name: ${names[index]}`;
        element.style.backgroundColor = 'lightblue'; // Highlight the picked name
        lastPickedIndex = index; // Update the last picked index
    }

    // Save and Load functionality - Placeholder for AJAX implementation
    // Ensure to replace URL paths and handle server responses according to your setup.
    // document.getElementById('saveListBtn')?.addEventListener('click', () => {
    //     const listName = document.getElementById('listName').value.trim();
    //     const selectedNames = names.filter((_, index) => selectedIndices.has(index));
    //     saveList(listName, selectedNames);
    // });

    function saveList(listName, namesArray) {
        // Example AJAX request to save the list
        console.log('Saving list:', listName, namesArray);
        // Implement actual AJAX request here
    }
	
	function saveList(listName, namesArray) {
    // Convert the data to be sent into JSON format
    const requestData = JSON.stringify({
        listName: listName,
        names: namesArray
    });

    // Perform the AJAX request using Fetch API
    fetch('/api/save_name_list.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: requestData
    })
    .then(response => response.json()) // Parsing the JSON response from the server
    .then(data => {
        if (data.success) {
            alert('List saved successfully!');
        } else {
            alert('Failed to save list. Error: ' + data.message);
        }
    })
    .catch(error => {
        // Handle any errors that occurred during the fetch operation
        console.error('Error saving list:', error);
        alert('An error occurred while saving the list.');
    });
}


