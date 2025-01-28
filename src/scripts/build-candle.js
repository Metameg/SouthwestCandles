import cartInstance from './cart';

(function () {
    const frageranceSelectors = document.querySelectorAll('.fragerance-selector');
    const wickSelectors = document.querySelectorAll('.wick-selector');
    const sizeSelectors = document.querySelectorAll('.size-selector');
    const productSummaryName = document.getElementById('productSummaryName');
    const productSummaryDesc = document.getElementById('productSummaryDesc');
    const productSummarySize = document.getElementById('productSummarySize');
    const productSummaryWick = document.getElementById('productSummaryWick');
    const productSummaryPrice = document.getElementById('productSummaryPrice');
    const addToCartBtn = document.getElementById('addBuildToCartBtn');

    // Function to check if all fields are populated
    function checkFieldsPopulated() {
        const fields = [
            productSummaryName,
            productSummaryDesc,
            productSummarySize,
            productSummaryWick,
            productSummaryPrice,
        ];

        const allPopulated = fields.every(field => {
            const text = field.textContent.trim();
            return text !== "" && text !== "--" && text !== "$--.--";
        });

        addToCartBtn.disabled = !allPopulated;
    }

    async function load() {
        // Clear the selected radios on page load
        frageranceSelectors.forEach(item => {
            const radio = item.querySelector('.custom-radio');
            radio.checked = false;
        });
        wickSelectors.forEach(item => {
            const radio = item.querySelector('.custom-radio');
            radio.checked = false;
        });
        sizeSelectors.forEach(item => {
            const radio = item.querySelector('.custom-radio');
            radio.checked = false;
        });

        frageranceSelectors.forEach(item => {
            const radio = item.querySelector('.custom-radio');
            const updateProductSummary = () => {
                const nameElement = item.querySelector('.name');
                const name = Array.from(nameElement.childNodes)
                .filter(node => node.nodeType === Node.TEXT_NODE)
                .map(node => node.textContent.trim())
                .join('');
                const description = item.querySelector('.description').textContent.trim();
    
                // Update the product summary dynamically
                productSummaryName.textContent = name;
                productSummaryDesc.textContent = description;

                // Check if all fields are populated
                checkFieldsPopulated();
            }

            item.addEventListener('click', function() {
                if (radio && !radio.checked) {
                    radio.checked = true;
                    updateProductSummary();   
                }
            });

            // Handle changes to the radio button
            radio.addEventListener('change', updateProductSummary);
        });

        // event listeners for wicks
        wickSelectors.forEach(item => {
            const radio = item.querySelector('.custom-radio');
            const wick = item.querySelector('.card-name');
            const size4oz = document.getElementById('size4oz');
            const radio4oz = size4oz.querySelector('.custom-radio');
            const name4oz = size4oz.querySelector('.card-name');
            const price = name4oz.querySelector('.price');
            const img4oz = size4oz.querySelector('.card-image');
            const woodErrorMsg = document.getElementById('woodWickErrorMsg')
            

            const updateProductSummary = () => {
                const isWoodWick = radio.value === 'wood' && radio.checked;

                if (isWoodWick) {
                    console.log(radio4oz);
                    size4oz.disabled = true;
                    woodErrorMsg.style.display = 'block';
                    size4oz.classList.add('disabled'); 
                    radio4oz.classList.add('disabled'); 
                    price.classList.add('disabled'); 
                    name4oz.classList.add('disabled'); 
                    img4oz.classList.add('disabled'); 
                } else {
                    size4oz.disabled = false;
                    woodErrorMsg.style.display = 'none';
                    size4oz.classList.remove('disabled');
                    radio4oz.classList.remove('disabled');
                    price.classList.remove('disabled');
                    name4oz.classList.remove('disabled');
                    img4oz.classList.remove('disabled');
                }
                // Update the product summary wick type
                productSummaryWick.textContent = wick.textContent.trim();

                // Check if all fields are populated
                checkFieldsPopulated();
            }

            item.addEventListener('click', function() {
                if (radio && !radio.checked) {
                    radio.checked = true;
                    updateProductSummary();
                }
            });

            // Handle changes to the radio button
            radio.addEventListener('change', updateProductSummary);
        });

        // event listeners for sizes
        sizeSelectors.forEach(item => {
            const radio = item.querySelector('.custom-radio');
            const updateProductSummary = () => {
                const sizeElement = item.querySelector('.card-name');
                const size = Array.from(sizeElement.childNodes)
                    .filter(node => node.nodeType === Node.TEXT_NODE)
                    .map(node => node.textContent.trim())
                    .join('');
                const rawPrice = sizeElement.querySelector('.price').textContent;
                const numericPrice = parseFloat(rawPrice.replace('$', '')); // Extract the numeric part
                const formattedPrice = `$${numericPrice.toFixed(2)}`;
        
                // Update the product summary
                productSummarySize.textContent = size.trim().replace('-', '');
                productSummaryPrice.textContent = formattedPrice;

                // Check if all fields are populated
                checkFieldsPopulated();
            };
        
            // Handle clicks on the entire card
            item.addEventListener('click', function() {
                if (!radio.checked) {
                    radio.checked = true; // Check the radio button
                    updateProductSummary();
                }
            });
        
            // Handle changes to the radio button
            radio.addEventListener('change', updateProductSummary);
        });

        addToCartBtn.addEventListener("click", function () {
            cartInstance.addBuildItem();
            console.log(cartInstance.cart);
        });

        // Check if all fields are populated
        checkFieldsPopulated();
    }

    load();
})();