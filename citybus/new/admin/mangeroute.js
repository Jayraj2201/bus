document.addEventListener("DOMContentLoaded", function () {
  fetch("mangeroute.php")
    .then(res => res.json())
    .then(data => renderTable(data))
    .catch(err => console.error("Error fetching data:", err));
});

function renderTable(data) {
  const tableHead = document.getElementById("tableHead");
  const tableBody = document.getElementById("tableBody");

  tableHead.innerHTML = '';
  tableBody.innerHTML = '';

  if (data.length === 0) {
    tableBody.innerHTML = "<tr><td colspan='100%'>No data found</td></tr>";
    return;
  }

  const keys = Object.keys(data[0]);
  keys.forEach(key => {
    tableHead.innerHTML += `<th>${key}</th>`;
  });
  tableHead.innerHTML += `<th>Action</th>`;

  data.forEach(row => {
    let html = "<tr>";
    keys.forEach(key => {
      html += `<td><input type="text" name="${key}" value="${row[key]}" readonly /></td>`;
    });
    html += `
      <td class="action-buttons">
        <button class="edit-btn btn btn-sm btn-primary me-1" onclick="enableEdit(this)">Edit</button>
        <button class="save-btn btn btn-sm btn-success" onclick="saveRow(this)" style="display:none;">Save</button>
      </td>`;
    html += "</tr>";
    tableBody.innerHTML += html;
  });
}

function enableEdit(button) {
  const row = button.closest("tr");
  row.querySelectorAll("td input").forEach(input => {
    input.removeAttribute("readonly");
    input.classList.add("editable");
  });
  row.querySelector(".edit-btn").style.display = "none";
  row.querySelector(".save-btn").style.display = "inline-block";
}

function saveRow(button) {
  const row = button.closest("tr");
  const inputs = row.querySelectorAll("td input");
  const updatedData = {};
  let id = null;

  inputs.forEach((input, index) => {
    const key = input.name;
    updatedData[key] = input.value;
    if (index === 0) id = input.value; // Assuming first column is ID
  });

  updatedData.id = id;

  fetch("mangeroute_save.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify(updatedData)
  })
    .then(res => res.text())
    .then(msg => {
      console.log("Save response:", msg);
      return fetch(`mangeroute_getrow.php?id=${id}`);
    })
    .then(res => res.json())
    .then(rowData => {
      inputs.forEach(input => {
        const name = input.name;
        input.value = rowData[name] || input.value;
        input.setAttribute("readonly", true);
        input.classList.remove("editable");
      });
      row.querySelector(".edit-btn").style.display = "inline-block";
      row.querySelector(".save-btn").style.display = "none";
    })
    .catch(err => console.error("Error saving/updating:", err));
}
