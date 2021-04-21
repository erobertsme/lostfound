## Lost and Found
#### WordPress Plugin
<br />

### Description
Creates a shortcode to display a form which allows users to submit to a Lost and Found custom post type with ACF custom fields. Use **[lostfound_form]** to display the form.

### Installation
Download the latest release (.zip file). Log into the WordPress dashboard. Under "Plugins" select "Add new". Click the "Upload plugin button near the top". Click "Choose file" and select the downloaded latest release, or drag and drop the file into the box.

https://wordpress.org/support/article/managing-plugins/#manual-upload-via-wordpress-admin
<br />

### TODO
- ~~Finish photo upload~~
- ~~Email notification~~
- ~~Improve acf_form_head to only load on pages the shortcode is used~~
- ~~Options page~~
- Shortcode to display the CPT data [lostfound_display]
- Zero Spam (need to re-implement after switching to ACF form)
- Captcha
- Options:
  - multiple notification email addresses
  - notification message
  - CPT slug
- Refactor options page
- Default styling (with admin page option to disable and shortcode attribute override)
- Customize CPT, Custom Taxonomy, and ACF fields (Would likely need to be renamed)

### Screenshots

![menu](https://user-images.githubusercontent.com/9193319/115627674-4297f500-a2b4-11eb-8321-9af692ec7b5e.png)
![settings](https://user-images.githubusercontent.com/9193319/115627671-41ff5e80-a2b4-11eb-9997-08ff929f4108.png)
<img src="https://user-images.githubusercontent.com/9193319/115627672-4297f500-a2b4-11eb-8def-e15324bd6c2b.png" width="400" height="809">