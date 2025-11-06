/**
 * Surajx GII Theme Main JavaScript
 *
 * @package Surajx_GII_Theme
 * @since 1.0.0
 */

(function($) {
    'use strict';

    // Dashboard Tabs Functionality
    function initDashboardTabs() {
        const tabButtons = document.querySelectorAll('.dashboard-tab');
        const tabPanels = document.querySelectorAll('.tab-panel');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabName = this.getAttribute('data-tab');

                // Remove active class from all tabs and panels
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanels.forEach(panel => panel.classList.remove('active'));

                // Add active class to clicked tab and corresponding panel
                this.classList.add('active');
                document.getElementById(tabName + '-panel').classList.add('active');

                // Load data for the active tab
                if (tabName === 'products') {
                    loadProducts();
                } else if (tabName === 'invoices') {
                    loadInvoices();
                } else if (tabName === 'account') {
                    loadAccountSettings();
                }
            });
        });

        // Load initial data for the first tab
        if (tabButtons.length > 0) {
            loadProducts();
        }
    }

    // Load Products from REST API
    function loadProducts() {
        const container = document.getElementById('productsListContainer');
        if (!container) return;

        container.innerHTML = '<p>Loading products...</p>';

        fetch(giiTheme.restUrl + 'products', {
            method: 'GET',
            headers: {
                'X-WP-Nonce': giiTheme.nonce
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                container.innerHTML = '<p>No products found. <button class="btn" id="addFirstProduct">Add Your First Product</button></p>';
            } else {
                let html = '<table style="width: 100%; border-collapse: collapse;">';
                html += '<thead><tr><th>Name</th><th>SKU</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>';
                html += '<tbody>';

                data.forEach(product => {
                    html += '<tr>';
                    html += '<td>' + escapeHtml(product.name) + '</td>';
                    html += '<td>' + escapeHtml(product.sku) + '</td>';
                    html += '<td>₹' + escapeHtml(product.price) + '</td>';
                    html += '<td>' + escapeHtml(product.stock) + '</td>';
                    html += '<td><button class="btn btn-secondary edit-product" data-id="' + product.id + '">Edit</button></td>';
                    html += '</tr>';
                });

                html += '</tbody></table>';
                container.innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Error loading products:', error);
            container.innerHTML = '<div class="alert alert-error">Failed to load products. Please try again.</div>';
        });
    }

    // Load Invoices from REST API
    function loadInvoices() {
        const container = document.getElementById('invoicesListContainer');
        if (!container) return;

        container.innerHTML = '<p>Loading invoices...</p>';

        fetch(giiTheme.restUrl + 'invoices', {
            method: 'GET',
            headers: {
                'X-WP-Nonce': giiTheme.nonce
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                container.innerHTML = '<p>No invoices found. <a href="' + giiTheme.siteUrl + '/invoice-builder" class="btn">Create Your First Invoice</a></p>';
            } else {
                let html = '<table style="width: 100%; border-collapse: collapse;">';
                html += '<thead><tr><th>Invoice #</th><th>Customer</th><th>Date</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>';
                html += '<tbody>';

                data.forEach(invoice => {
                    html += '<tr>';
                    html += '<td>' + escapeHtml(invoice.invoice_number) + '</td>';
                    html += '<td>' + escapeHtml(invoice.customer_name) + '</td>';
                    html += '<td>' + escapeHtml(invoice.date) + '</td>';
                    html += '<td>₹' + escapeHtml(invoice.total) + '</td>';
                    html += '<td>' + escapeHtml(invoice.status) + '</td>';
                    html += '<td><button class="btn btn-secondary view-invoice" data-id="' + invoice.id + '">View</button></td>';
                    html += '</tr>';
                });

                html += '</tbody></table>';
                container.innerHTML = html;
            }
        })
        .catch(error => {
            console.error('Error loading invoices:', error);
            container.innerHTML = '<div class="alert alert-error">Failed to load invoices. Please try again.</div>';
        });
    }

    // Load Account Settings
    function loadAccountSettings() {
        const form = document.getElementById('accountSettingsForm');
        if (!form) return;

        fetch(giiTheme.restUrl + 'account/settings', {
            method: 'GET',
            headers: {
                'X-WP-Nonce': giiTheme.nonce
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('company_name').value = data.company_name || '';
            document.getElementById('gstin').value = data.gstin || '';
            document.getElementById('company_address').value = data.company_address || '';
        })
        .catch(error => {
            console.error('Error loading account settings:', error);
        });
    }

    // Save Account Settings
    function saveAccountSettings(e) {
        e.preventDefault();

        const formData = {
            company_name: document.getElementById('company_name').value,
            gstin: document.getElementById('gstin').value,
            company_address: document.getElementById('company_address').value
        };

        fetch(giiTheme.restUrl + 'account/settings', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': giiTheme.nonce
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            alert('Settings saved successfully!');
        })
        .catch(error => {
            console.error('Error saving settings:', error);
            alert('Failed to save settings. Please try again.');
        });
    }

    // Invoice Builder Functionality
    function initInvoiceBuilder() {
        const form = document.getElementById('invoiceBuilderForm');
        if (!form) return;

        // Add item button
        const addItemBtn = document.getElementById('addItemBtn');
        if (addItemBtn) {
            addItemBtn.addEventListener('click', addInvoiceItem);
        }

        // Calculate totals on input change
        form.addEventListener('input', function(e) {
            if (e.target.matches('[name="item_qty[]"], [name="item_rate[]"], [name="item_gst[]"]')) {
                calculateInvoiceTotals();
            }
        });

        // Remove item buttons
        form.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-item')) {
                e.preventDefault();
                const row = e.target.closest('tr');
                if (document.querySelectorAll('.invoice-item-row').length > 1) {
                    row.remove();
                    calculateInvoiceTotals();
                } else {
                    alert('At least one item is required.');
                }
            }
        });

        // Form submission
        form.addEventListener('submit', submitInvoice);
    }

    // Add Invoice Item Row
    function addInvoiceItem() {
        const tbody = document.getElementById('invoiceItemsBody');
        const newRow = document.createElement('tr');
        newRow.className = 'invoice-item-row';
        newRow.innerHTML = `
            <td><input type="text" name="item_name[]" required></td>
            <td><input type="number" name="item_qty[]" min="1" value="1" required></td>
            <td><input type="number" name="item_rate[]" min="0" step="0.01" required></td>
            <td><input type="number" name="item_gst[]" min="0" max="100" step="0.01" value="18" required></td>
            <td class="item-amount">0.00</td>
            <td><button type="button" class="btn btn-secondary remove-item">Remove</button></td>
        `;
        tbody.appendChild(newRow);
    }

    // Calculate Invoice Totals
    function calculateInvoiceTotals() {
        let subtotal = 0;
        let totalGst = 0;

        document.querySelectorAll('.invoice-item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('[name="item_qty[]"]').value) || 0;
            const rate = parseFloat(row.querySelector('[name="item_rate[]"]').value) || 0;
            const gstPercent = parseFloat(row.querySelector('[name="item_gst[]"]').value) || 0;

            const itemSubtotal = qty * rate;
            const itemGst = (itemSubtotal * gstPercent) / 100;
            const itemTotal = itemSubtotal + itemGst;

            row.querySelector('.item-amount').textContent = itemTotal.toFixed(2);

            subtotal += itemSubtotal;
            totalGst += itemGst;
        });

        const total = subtotal + totalGst;

        document.getElementById('subtotal').textContent = subtotal.toFixed(2);
        document.getElementById('gstAmount').textContent = totalGst.toFixed(2);
        document.getElementById('totalAmount').textContent = total.toFixed(2);
    }

    // Submit Invoice
    function submitInvoice(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);
        const invoiceData = {
            from: {
                company: formData.get('from_company'),
                gstin: formData.get('from_gstin'),
                address: formData.get('from_address')
            },
            to: {
                company: formData.get('to_company'),
                gstin: formData.get('to_gstin'),
                address: formData.get('to_address')
            },
            items: []
        };

        const itemNames = formData.getAll('item_name[]');
        const itemQtys = formData.getAll('item_qty[]');
        const itemRates = formData.getAll('item_rate[]');
        const itemGsts = formData.getAll('item_gst[]');

        for (let i = 0; i < itemNames.length; i++) {
            invoiceData.items.push({
                name: itemNames[i],
                quantity: parseFloat(itemQtys[i]),
                rate: parseFloat(itemRates[i]),
                gst: parseFloat(itemGsts[i])
            });
        }

        fetch(giiTheme.restUrl + 'invoices', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': giiTheme.nonce
            },
            body: JSON.stringify(invoiceData)
        })
        .then(response => response.json())
        .then(data => {
            alert('Invoice created successfully!');
            window.location.href = giiTheme.siteUrl + '/dashboard';
        })
        .catch(error => {
            console.error('Error creating invoice:', error);
            alert('Failed to create invoice. Please try again.');
        });
    }

    // Google Sign-In
    function initGoogleSignIn() {
        const googleBtn = document.getElementById('googleSignInBtn');
        if (!googleBtn) return;

        googleBtn.addEventListener('click', function() {
            const redirect = this.getAttribute('data-redirect');
            // This would connect to the plugin's Google OAuth endpoint
            window.location.href = giiTheme.restUrl + 'auth/google?redirect=' + encodeURIComponent(redirect);
        });
    }

    // Utility function to escape HTML
    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    // Initialize on document ready
    $(document).ready(function() {
        initDashboardTabs();
        initInvoiceBuilder();
        initGoogleSignIn();

        // Account settings form submission
        const accountForm = document.getElementById('accountSettingsForm');
        if (accountForm) {
            accountForm.addEventListener('submit', saveAccountSettings);
        }

        // Add product button
        $(document).on('click', '#addProductBtn, #addFirstProduct', function() {
            // This would open a modal or redirect to add product page
            alert('Add product functionality would connect to the plugin REST API.');
        });

        // Initial invoice calculation
        if (document.getElementById('invoiceBuilderForm')) {
            calculateInvoiceTotals();
        }
    });

})(jQuery);
