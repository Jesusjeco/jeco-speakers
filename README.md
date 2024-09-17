# Jeco Speakers Manager Plugin

**Contributors**: Jesus Carrero - jesusenrique.carrero@gmail.com 
**Tags**: speakers, management, custom post type, CRUD  
**Requires at least**: 5.0  
**Tested up to**: 6.3  
**Requires PHP**: 7.0  
**Stable tag**: 1.0.0  
**License**: GPLv2 or later  
**License URI**: https://www.gnu.org/licenses/gpl-2.0.html  

Manage a list of speakers in your WordPress dashboard. Easily add, edit, and delete speaker profiles using a simple admin interface.

## Description

The **Jeco Speakers Manager Plugin** allows you to manage speakers directly from your WordPress dashboard. It provides functionality for creating, editing, and deleting speaker profiles through a clean and user-friendly interface.

### Features:

- **Add New Speakers**: Create new speaker profiles with fields like name, last name, email, phone, and location.
- **Edit Speakers**: Update the details of existing speakers.
- **Delete Speakers**: Remove speakers from the list with a simple confirmation prompt.
- **Security**: Protects against unauthorized actions using WordPress nonces for each CRUD operation.
- **Redirects**: After completing an action (add/edit/delete), users are redirected back to the speakers list.
- **Admin Notifications**: Success or failure notifications are displayed after each operation.

## Installation

### Manual Installation

After updating ad Activating the plugin through the 'Plugins' screen in WordPress, navigate to **Jeco Speakers** in the WordPress admin menu to manage speakers.

### Requirements

- PHP version 7.0 or higher.
- WordPress 5.0 or higher.

## Usage

1. After installing and activating the plugin, you will find a **Jeco Speakers** option in your WordPress admin menu.
2. From here, you can:
   - **Add** new speakers by filling out a simple form.
   - **Edit** existing speakers by modifying their details.
   - **Delete** speakers from the list with a confirmation prompt.

## Security

All CRUD operations are secured with WordPress nonces, ensuring that unauthorized requests are rejected.

## Frequently Asked Questions

### How do I add a speaker?

Navigate to the **Jeco Speakers** menu item in your WordPress dashboard and click **Add New Speaker**. Fill in the necessary details and click **Save**.

### How do I edit a speaker?

Click the **Edit** link next to the speaker in the speakers list, update the fields, and click **Update Speaker**.

### How do I delete a speaker?

Click the **Delete** link next to the speaker in the list. You will be asked for confirmation before the speaker is permanently removed.

## Changelog

### 1.0.0
- Initial release with CRUD operations for speaker management.