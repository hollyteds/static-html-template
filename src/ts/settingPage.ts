document.addEventListener('DOMContentLoaded', function() {
  document.querySelector('.add-row').addEventListener('click', function() {
      const table = document.getElementById('dynamic-form') as HTMLTableElement;
      const rowCount: number = table.rows.length - 1;
      const newRow: HTMLTableRowElement = table.insertRow(-1);
      newRow.innerHTML = `
          <td><input type="text" name="my_custom_settings[${rowCount}][id]"></td>
          <td><input type="text" name="my_custom_settings[${rowCount}][url]"></td>
          <td><button type="button" class="button remove-row">Remove</button></td>
      `;
  });
  document.getElementById('dynamic-form').addEventListener('click', function(event: Event) {
      if (event.target && (event.target as Element).matches('button.remove-row')) {
          (event.target as Element).closest('tr').remove();
      }
  });
});