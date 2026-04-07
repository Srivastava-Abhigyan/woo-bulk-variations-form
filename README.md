# WooCommerce Bulk Variations Form 🛒

[![WordPress Version](https://img.shields.io/badge/WordPress-5.0+-blue.svg)](https://wordpress.org/)
[![WooCommerce Version](https://img.shields.io/badge/WooCommerce-4.0+-purple.svg)](https://woocommerce.com/)
[![License](https://img.shields.io/badge/license-GPL--2.0-orange.svg)](LICENSE)

**WooCommerce Bulk Variations Form** is a powerful WordPress plugin that streamlines the ordering process for variable products. Instead of adding variations one by one, customers can select multiple quantities for different attribute combinations and add them all to their cart with a single click.

Perfect for wholesale stores, B2B platforms, and any shop where users need to buy multiple variations (like different colors and sizes) at once.

---

## ✨ Features

- **🚀 Bulk Add to Cart**: Add multiple variations with different quantities in one go.
- **💰 Real-time Pricing**: Automatically fetches and displays the price for each selected attribute combination via AJAX.
- **📊 Order Summary**: Live calculation of total items and grand total before adding to cart.
- **💾 Form Persistence**: Remembers selections using `localStorage`, so users don't lose their progress if they refresh the page.
- **⚡ AJAX Enabled**: Smooth, no-reload experience for adding items to the cart.
- **🎨 Responsive Design**: Optimized table layout for easy selection on all devices.
- **🛠 Easy Integration**: Automatically attaches to the variations form on product pages.

---

## 📸 Preview

![Bulk Form Preview](https://github.com/user-attachments/assets/placeholder-image-url)
*(Replace this placeholder with an actual screenshot of your plugin in action!)*

---

## 🛠 Installation

1. **Download** the repository as a ZIP file.
2. **Login** to your WordPress Admin Dashboard.
3. Navigate to **Plugins** > **Add New**.
4. Click **Upload Plugin** and select the ZIP file.
5. **Activate** the plugin.

Alternatively, you can upload the folder directly to your `/wp-content/plugins/` directory via FTP.

---

## 📖 Usage

1. Open any **Variable Product** page in your WooCommerce store.
2. Scroll down to the **Bulk Order** section below the standard variations form.
3. Select the desired attributes for each row.
4. Enter the quantities for each variation.
5. Review the **Total Items** and **Grand Total**.
6. Click **Add to Cart** to process the bulk order.

---

## 🔧 Technical Details

- **Hooks Used**: 
  - `woocommerce_after_variations_form`: Renders the bulk form.
  - `wp_ajax_bulk_add_to_cart`: Handles the multi-item cart addition.
  - `wp_ajax_get_variation_price`: Fetches dynamic pricing for variations.
- **Frontend**: Vanilla JavaScript (jQuery) and CSS.
- **Persistence**: Product-specific state saved in `localStorage`.

---

## 📝 Planned Improvements

- [ ] Support for custom variations table templates.
- [ ] Option to add/remove rows dynamically.
- [ ] Integration with Stock Management to show availability in real-time.
- [ ] Shortcode support for rendering the form anywhere.

---

## 📜 License

This project is licensed under the GPLv2 License - see the [LICENSE](LICENSE) file for details.

---

## 🤝 Contributing

Contributions are welcome! If you have ideas for features or find bugs, feel free to open an issue or submit a pull request.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

*Made with ❤️ by A.DEV for WooCommerce Store Owners.*
