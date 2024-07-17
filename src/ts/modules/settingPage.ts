/**
 * Adds a new row to the dynamic form table when the "Add Row" button is clicked.
 * Also handles the removal of rows when the "Remove" button is clicked.
 */
export default function addRow() {

    document.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.add-row').addEventListener('click', function() {
        const table = document.getElementById('replacement-form') as HTMLTableElement;
        const rowCount: number = table.rows.length;
        const newRow: HTMLTableRowElement = table.insertRow(-1);
        console.log(rowCount);
        newRow.innerHTML = `
            <tr>
                <td>
                    <input type="text" name="scht_settings[replacement][${rowCount}][target]" size="50" placeholder="" value="">
                </td>
                <td>=&gt;</td>
                <td>
                    <input type="text" name="scht_settings[replacement][${rowCount}][new]" size="50" placeholder="" value="">
                </td>
                <td>
                    <button type="button" class="button remove-row">Remove</button>
                </td>
            </tr>
        `;
    });
    document.getElementById('replacement-form').addEventListener('click', function(event: Event) {
        if (event.target && (event.target as Element).matches('button.remove-row')) {
            (event.target as Element).closest('tr').remove();
        }
    });
    });  
            
}

