use std::collections::HashMap;

pub fn english_translations() -> HashMap<&'static str, &'static str> {
    let mut map = HashMap::new();
    map.insert("login.welcome", "Welcome to Jinya");
    map.insert("login.username", "Email address");
    map.insert("login.password", "Password");
    map.insert("login.credits", "Photo from ");
    map.insert("login.action_login", "Login");
    map.insert("login.action_two_factor", "Request second factor");
    map.insert("login.error_invalid_password", "Wrong email or password");

    map.insert("2fa.header", "Your two factor code");
    map.insert("2fa.code", "Two factor code");
    map.insert("2fa.credits", "Photo from ");
    map.insert("2fa.action_login", "Login");
    map.insert("2fa.error_code", "The code is invalid");

    map.insert("home.credits", "Photo from ");

    map.insert("app.menu.search", "Search…");
    map.insert("app.menu.content", "Content");
    map.insert("app.menu.content.media", "Media");
    map.insert("app.menu.content.media.files", "Files");
    map.insert("app.menu.content.media.galleries", "Galleries");
    map.insert("app.menu.content.pages", "Pages");
    map.insert("app.menu.content.pages.simple_pages", "Simple Pages");
    map.insert("app.menu.content.pages.simple_pages.edit", "Edit page");
    map.insert("app.menu.content.pages.simple_pages.add", "Add page");
    map.insert("app.menu.content.pages.segment_pages", "Segment Pages");

    map.insert("app.menu.my_jinya", "My Jinya");
    map.insert("app.menu.my_jinya.my_account", "My account");
    map.insert("app.menu.my_jinya.my_account.my_profile", "My profile");
    map.insert("app.menu.my_jinya.my_account.change_password", "Change password");
    map.insert("app.menu.my_jinya.my_account.logout", "Logout");

    map.insert("app.title.home_page", "Startpage");

    map.insert("files.card.button.information", "More info");
    map.insert("files.card.button.edit", "Edit file");
    map.insert("files.card.button.delete", "Delete file");

    map.insert("files.details.created_by", "Created by");
    map.insert("files.details.created_at", "Created at");
    map.insert("files.details.last_updated_by", "Last updated by");
    map.insert("files.details.last_updated_at", "Last updated at");

    map.insert("files.delete.approve", "Delete file");
    map.insert("files.delete.decline", "Keep file");
    map.insert("files.delete.title", "Really delete file?");
    map.insert("files.delete.content", "Are you sure, that you want to delete \"{name}\". The file will be removed from all galleries and themes.");
    map.insert("files.delete.failed", "The file \"{name}\" could not be deleted.");

    map.insert("files.edit.action_save", "Save changes");
    map.insert("files.edit.action_discard", "Discard changes");
    map.insert("files.edit.name", "Name");
    map.insert("files.edit.file", "File");
    map.insert("files.edit.file_selected", "File selected");
    map.insert("files.edit.saved.success", "Successfully saved {name}");
    map.insert("files.edit.error_generic", "The file could not be saved");
    map.insert("files.edit.error_conflict", "The file could not be saved, because there is a file with the same name");
    map.insert("files.edit.error_filename_empty", "The name must be not empty");
    map.insert("files.edit.saving", "Saving the file");
    map.insert("files.edit.title", "Update {name}");

    map.insert("files.add.action_save", "Save file");
    map.insert("files.add.action_discard", "Discard file");
    map.insert("files.add.name", "Name");
    map.insert("files.add.file", "File");
    map.insert("files.add.select_file", "Select file");
    map.insert("files.add.saved.success", "Successfully saved {name}");
    map.insert("files.add.error_generic", "The file could not be saved");
    map.insert("files.add.error_conflict", "The file could not be saved, because there is a file with the same name");
    map.insert("files.add.error_filename_empty", "The name must be not empty");
    map.insert("files.add.error_file_missing", "You have to select a file");
    map.insert("files.add.saving", "Saving the file");
    map.insert("files.add.title", "New file}");

    map.insert("galleries.overview.action_new", "New gallery");
    map.insert("galleries.overview.action_edit", "Edit gallery");
    map.insert("galleries.overview.action_designer", "Gallery designer");
    map.insert("galleries.overview.action_delete", "Delete gallery");
    map.insert("galleries.overview.table.name_column", "Name");
    map.insert("galleries.overview.table.orientation_column", "Orientation");
    map.insert("galleries.overview.table.type_column", "Type");
    map.insert("galleries.overview.table.description_column", "Description");
    map.insert("galleries.overview.table.orientation.horizontal", "Horizontal");
    map.insert("galleries.overview.table.orientation.vertical", "Vertical");
    map.insert("galleries.overview.table.type.masonry", "Masonry");
    map.insert("galleries.overview.table.type.sequence", "Sequence");

    map.insert("galleries.delete.approve", "Delete gallery");
    map.insert("galleries.delete.decline", "Keep gallery");
    map.insert("galleries.delete.title", "Really delete gallery?");
    map.insert("galleries.delete.content", "Are you sure, that you want to delete \"{name}\". The gallery will be removed from all menus and themes.");
    map.insert("galleries.delete.failed", "The gallery \"{name}\" could not be deleted.");

    map.insert("galleries.add.title", "Add gallery");
    map.insert("galleries.add.name", "Name");
    map.insert("galleries.add.orientation", "Orientation");
    map.insert("galleries.add.orientation.vertical", "Vertical");
    map.insert("galleries.add.orientation.horizontal", "Horizontal");
    map.insert("galleries.add.type", "Type");
    map.insert("galleries.add.type.sequence", "Sequence");
    map.insert("galleries.add.type.masonry", "Masonry");
    map.insert("galleries.add.description", "Description");
    map.insert("galleries.add.error_name_empty", "The name is required");
    map.insert("galleries.add.action_discard", "Discard gallery");
    map.insert("galleries.add.action_save", "Save gallery");
    map.insert("galleries.add.saving", "Saving the gallery");
    map.insert("galleries.add.saved", "The gallery {name} was saved successfully.");
    map.insert("galleries.add.error_conflict", "The gallery could not be saved, because there is a gallery with the same name");
    map.insert("galleries.add.error_generic", "The gallery could not be saved");

    map.insert("galleries.edit.title", "Edit {name}");
    map.insert("galleries.edit.name", "Name");
    map.insert("galleries.edit.orientation", "Orientation");
    map.insert("galleries.edit.orientation.vertical", "Vertical");
    map.insert("galleries.edit.orientation.horizontal", "Horizontal");
    map.insert("galleries.edit.type", "Type");
    map.insert("galleries.edit.type.sequence", "Sequence");
    map.insert("galleries.edit.type.masonry", "Masonry");
    map.insert("galleries.edit.description", "Description");
    map.insert("galleries.edit.error_name_empty", "The name is required");
    map.insert("galleries.edit.action_discard", "Discard gallery");
    map.insert("galleries.edit.action_save", "Save gallery");
    map.insert("galleries.edit.saving", "Saving the gallery");
    map.insert("galleries.edit.saved", "The gallery {name} was saved successfully.");
    map.insert("galleries.edit.error_conflict", "The gallery could not be saved, because there is a gallery with the same name");
    map.insert("galleries.edit.error_generic", "The gallery could not be saved");

    map.insert("galleries.designer.error_delete", "The file could not be removed");
    map.insert("galleries.designer.error_add", "The file could not be added");
    map.insert("galleries.designer.error_update", "The file could not be moved");

    map.insert("simple_pages.overview.action_new", "New page");
    map.insert("simple_pages.overview.action_edit", "Edit page");
    map.insert("simple_pages.overview.action_delete", "Delete page");
    map.insert("simple_pages.overview.table.title_column", "Title");
    map.insert("simple_pages.overview.table.content_column", "Content");

    map.insert("simple_pages.delete.approve", "Delete page");
    map.insert("simple_pages.delete.decline", "Keep page");
    map.insert("simple_pages.delete.title", "Really delete page?");
    map.insert("simple_pages.delete.content", "Are you sure, that you want to delete \"{title}\". The page will be removed from all menus and themes.");
    map.insert("simple_pages.delete.failed", "The page \"{title}\" could not be deleted.");

    map.insert("simple_pages.edit.title", "Title");
    map.insert("simple_pages.edit.error_empty_title", "The title is required");
    map.insert("simple_pages.edit.update", "Save page");
    map.insert("simple_pages.edit.error_conflict", "The page could not be saved, because there is a page with the same name");
    map.insert("simple_pages.edit.error_generic", "The page could not be saved");

    map.insert("simple_pages.add.title", "Title");
    map.insert("simple_pages.add.error_empty_title", "The title is required");
    map.insert("simple_pages.add.create", "Save page");
    map.insert("simple_pages.add.error_conflict", "The page could not be saved, because there is a page with the same name");
    map.insert("simple_pages.add.error_generic", "The page could not be saved");

    map.insert("segment_pages.overview.action_new", "New page");
    map.insert("segment_pages.overview.action_edit", "Edit page");
    map.insert("segment_pages.overview.action_designer", "Page designer");
    map.insert("segment_pages.overview.action_delete", "Delete page");
    map.insert("segment_pages.overview.table.name_column", "Name");
    map.insert("segment_pages.overview.table.segment_count_column", "Segment count");

    map.insert("segment_pages.add.title", "Add page");
    map.insert("segment_pages.add.name", "Name");
    map.insert("segment_pages.add.error_name_empty", "The name is required");
    map.insert("segment_pages.add.action_discard", "Discard page");
    map.insert("segment_pages.add.action_save", "Save page");
    map.insert("segment_pages.add.saving", "Saving the page");
    map.insert("segment_pages.add.saved", "The page {name} was saved successfully.");
    map.insert("segment_pages.add.error_conflict", "The page could not be saved, because there is a page with the same name");
    map.insert("segment_pages.add.error_generic", "The page could not be saved");

    map.insert("segment_pages.edit.title", "Edit {name}");
    map.insert("segment_pages.edit.name", "Name");
    map.insert("segment_pages.edit.error_name_empty", "The name is required");
    map.insert("segment_pages.edit.action_discard", "Discard changes");
    map.insert("segment_pages.edit.action_save", "Save page");
    map.insert("segment_pages.edit.saving", "Saving the page");
    map.insert("segment_pages.edit.saved", "The page {name} was saved successfully.");
    map.insert("segment_pages.edit.error_conflict", "The page could not be saved, because there is a page with the same name");
    map.insert("segment_pages.edit.error_generic", "The page could not be saved");

    map.insert("segment_pages.delete.approve", "Delete page");
    map.insert("segment_pages.delete.decline", "Keep page");
    map.insert("segment_pages.delete.title", "Really delete page?");
    map.insert("segment_pages.delete.content", "Are you sure, that you want to delete \"{name}\". The page will be removed from all menus and themes.");
    map.insert("segment_pages.delete.failed", "The page \"{name}\" could not be deleted.");

    map.insert("segment_pages.designer.action", "Action");
    map.insert("segment_pages.designer.action.script", "Script");
    map.insert("segment_pages.designer.action.link", "Link");
    map.insert("segment_pages.designer.action.none", "None");
    map.insert("segment_pages.designer.target", "Target");
    map.insert("segment_pages.designer.script", "Script");
    map.insert("segment_pages.designer.gallery", "Gallery");
    map.insert("segment_pages.designer.html", "Formatted text");
    map.insert("segment_pages.designer.file", "File");
    map.insert("segment_pages.designer.error_load_galleries", "Failed to load the galleries");
    map.insert("segment_pages.designer.error_load_files", "Failed to load the files");
    map.insert("segment_pages.designer.error_load_segments", "Failed to load the segments");
    map.insert("segment_pages.designer.delete.approve", "Delete segment");
    map.insert("segment_pages.designer.delete.decline", "Keep segment");
    map.insert("segment_pages.designer.delete.title", "Really delete segment?");
    map.insert("segment_pages.designer.delete.content", "Are you sure, that you want to delete the selected segment?");
    map.insert("segment_pages.designer.delete.failed", "The segment could not be deleted.");
    map.insert("segment_pages.designer.action_save_segment", "Save changes");
    map.insert("segment_pages.designer.action_discard_segment", "Discard changes");
    map.insert("segment_pages.designer.error_change_segment_failed", "The segment could not be changed");
    map.insert("segment_pages.designer.error_create_segment_failed", "The segment could not be created");

    map
}