use anyhow::Error;
use http::Response;
use serde_derive::*;
use yew::Callback;
use yew::format::Json;
use yew::services::fetch::{FetchService, FetchTask};

use crate::ajax::{AjaxError, bool_handler, delete, get_error_from_parts, get_host, get_request, post_request_with_body, put_request, put_request_with_body};
use crate::models::list_model::ListModel;
use crate::models::menu_item::MenuItem;

pub struct MenuItemService {}

impl MenuItemService {
    pub fn new() -> MenuItemService {
        MenuItemService {}
    }

    pub fn get_by_menu(&self, id: usize, callback: Callback<Result<Vec<MenuItem>, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/menu/{}/item", get_host(), id);
        let request = get_request(url);
        let handler = move |response: Response<Json<Result<Vec<MenuItem>, Error>>>| {
            let (meta, Json(data)) = response.into_parts();
            if meta.status.is_success() {
                callback.emit(Ok(data.unwrap()));
            } else {
                callback.emit(get_error_from_parts(meta));
            }
        };

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn delete_menu_item_menu_item_parent(&self, menu_item_id: usize, position: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/menu-item/{}/item/{}", get_host(), menu_item_id, position);

        delete(url, callback)
    }

    pub fn delete_menu_item_menu_parent(&self, menu_id: usize, position: usize, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/menu/{}/item/{}", get_host(), menu_id, position);

        delete(url, callback)
    }

    pub fn change_menu_item_parent(&self, new_parent_id: usize, item: MenuItem, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/menu-item/{}/move/parent/to/item/{}", get_host(), item.id, new_parent_id);

        let request = put_request(url);
        let handler = bool_handler(callback);

        FetchService::fetch(request, handler.into()).unwrap()
    }

    pub fn move_item_one_level_up(&self, menu_id: usize, item: MenuItem, callback: Callback<Result<bool, AjaxError>>) -> FetchTask {
        let url = format!("{}/api/menu/{}/item/{}/move/parent/one/level/up", get_host(), menu_id, item.id);

        let request = put_request(url);
        let handler = bool_handler(callback);

        FetchService::fetch(request, handler.into()).unwrap()
    }
}