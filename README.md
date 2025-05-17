# BuddyBoss Profile Frames MS

A WordPress plugin that enables Lottie animation frames for user profiles in BuddyBoss multisite installations.

## Description

BuddyBoss Profile Frames MS allows multisite administrators to upload, manage, and provide Lottie animation frames that users can select to display around their profile avatars. The plugin is specifically designed for BuddyBoss multisite environments where the primary site displays user profiles.

### Key Features

- **Admin Management Interface**: Upload, preview, and delete Lottie animation files
- **User Selection Interface**: Users can select their preferred profile frame
- **Resource Optimization**: Frames only display on user profile pages, not on activity feeds, member searches, or messages
- **Multisite Compatible**: Designed specifically for BuddyBoss multisite installations

## Requirements

- WordPress Multisite installation
- BuddyBoss Platform plugin
- PHP 7.2 or higher

## Installation

1. Download the plugin zip file
2. In your WordPress Network Admin, go to Plugins > Add New > Upload Plugin
3. Upload the zip file and activate the plugin network-wide
4. The plugin functionality will be available on the main site of your multisite network

## Configuration (For Multisite Administrators)

### Managing Profile Frames

1. Log in as a network administrator to the main site of your multisite installation
2. Navigate to BuddyBoss > Lottie Profile Frames in the admin menu
3. Upload new Lottie animation frames:
   - Enter a descriptive name for the frame
   - Upload a Lottie animation file (JSON format)
   - Click "Upload Frame"
4. Manage existing frames:
   - Preview all uploaded frames
   - Delete frames that are no longer needed

### Setting Up the User Interface

1. Create a new page on your site (or edit an existing one)
2. Add the shortcode `[bb_profile_frame_selector]` to the page content
3. Publish or update the page
4. Users can now visit this page to select their profile frame

## User Instructions

### Selecting a Profile Frame

1. Log in to your account
2. Visit the profile frame selection page (set up by your administrator)
3. Click on any frame to select it (or choose "None" to remove your current frame)
4. Click "Save Selection" to apply the change
5. Visit your profile to see your new frame

## Technical Details

- Lottie animation files are stored in the `lottie_assets` folder within the plugin directory
- User frame selections are stored in user meta with the key `_bbpfms_selected_frame`
- Frames only load on profile pages to reduce resource usage across the site

## Troubleshooting

### Frames Not Displaying

- Ensure the Lottie animation file is valid and properly formatted
- Check that the user has selected a frame and saved their selection
- Verify that you're viewing the profile on the main site of your multisite network

### Upload Errors

- Ensure your Lottie files are valid JSON files
- Check that your server has write permissions to the plugin's `lottie_assets` directory
- Verify that the file size doesn't exceed your server's upload limits

## Support

For support requests, please open an issue on the GitHub repository or contact the plugin author.

## Credits

This plugin uses the [Lottie-Player](https://github.com/LottieFiles/lottie-player) web component for rendering Lottie animations.

## License

This plugin is licensed under the GPL v2 or later.
