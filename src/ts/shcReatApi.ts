import { __ } from '@wordpress/i18n';

document.addEventListener('DOMContentLoaded', function () {
  const selectBox = document.getElementById('shct-template-id');
  const metaDataDiv = document.getElementById('shct-dynasmic-link');

  selectBox.addEventListener('change', function () {

      const postId = selectBox.value;
      if (postId) {
          fetch(`${shtRestApi.rest_url}/${postId}`, {
              method: 'GET',
              headers: {
                  'X-WP-Nonce': shtRestApi.nonce
              }
          })
          .then(response => response.json())
            .then(data => {
              if (data) {
                  metaDataDiv.innerHTML = '<hr><p class="description">' + __( 'Replace the shortcodes set in this template\'s link with the entered strings for each item.', 'static-html-template' ) + '</p>';
                  for (const value of data) {
                      const div = document.createElement('div');
                      div.innerHTML = `<label for="scht_replace_link[${value}]">[${value}]</label><input type="text" name="scht_replace_link[${value}]" size="100" value="">`;
                      metaDataDiv.appendChild(div);
                  }
              }
          })
          .catch(error => {
              console.error('Error fetching meta data:', error);
              metaDataDiv.innerHTML = '';
          });
      } else {
          metaDataDiv.innerHTML = '';
      }
  });
});

