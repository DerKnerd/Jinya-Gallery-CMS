use jinya_ui::widgets::menu::bar::MenuBar;
use jinya_ui::widgets::menu::item::{MenuItem, SubItem, SubItemGroup};
use yew::prelude::*;
use yew::services::fetch::FetchTask;
use yew_router::{prelude::*, Switch};
use yew_router::agent::RouteRequest;
use yew_router::router::Render;

use crate::agents::menu_agent::{MenuAgent, MenuAgentRequest, MenuAgentResponse};
use crate::ajax::authentication_service::AuthenticationService;
use crate::i18n::Translator;
use crate::storage::AuthenticationStorage;
use crate::views::authentication::login::LoginPage;
use crate::views::authentication::two_factor::TwoFactorPage;
use crate::views::files::FilesPage;
use crate::views::galleries::designer::GalleryDesignerPage;
use crate::views::galleries::GalleriesPage;
use crate::views::home::HomePage;
use crate::views::segment_pages::designer::SegmentPageDesignerPage;
use crate::views::segment_pages::SegmentPagesPage;
use crate::views::simple_pages::add_page::AddSimplePagePage;
use crate::views::simple_pages::edit_page::EditSimplePagePage;
use crate::views::simple_pages::SimplePagesPage;

pub struct JinyaDesignerApp {
    link: ComponentLink<Self>,
    route_service: RouteService<()>,
    route: Route<()>,
    check_api_key_task: Option<FetchTask>,
    router: Box<dyn Bridge<RouteAgent>>,
    menu_agent: Box<dyn Bridge<MenuAgent>>,
    menu_title: String,
    translator: Translator,
    hide_search: bool,
}

#[derive(Switch, Clone, PartialEq)]
pub enum AppRoute {
    #[to = "/login"]
    Login,
    #[to = "/two-factor"]
    TwoFactor,
    #[to = "/content/files"]
    Files,
    #[to = "/content/galleries/{id}/designer"]
    GalleryDesigner(usize),
    #[to = "/content/galleries"]
    Galleries,
    #[to = "/content/simple-pages/{id}/edit"]
    EditSimplePage(usize),
    #[to = "/content/simple-pages/add"]
    AddSimplePage,
    #[to = "/content/simple-pages"]
    SimplePages,
    #[to = "/content/segment-pages/{id}/designer"]
    SegmentPageDesigner(usize),
    #[to = "/content/segment-pages"]
    SegmentPages,
    #[to = "/account/me"]
    MyProfile,
    #[to = "/"]
    Homepage,
}

pub enum Msg {
    OnRouteChanged(Route<()>),
    OnAuthenticationChecked(bool),
    OnMenuAgentResponse(MenuAgentResponse),
    OnMenuKeyword(String),
    OnMenuSearch(String),
    OnChangePassword(MouseEvent),
    OnLogout(MouseEvent),
    Ignore,
}

impl Component for JinyaDesignerApp {
    type Message = Msg;
    type Properties = ();

    fn create(_props: Self::Properties, link: ComponentLink<Self>) -> Self {
        let mut route_service: RouteService<()> = RouteService::new();
        let callback = link.callback(Msg::OnRouteChanged);
        route_service.register_callback(callback);

        let api_key = AuthenticationStorage::get_api_key();
        let mut check_api_key_task = None;
        let route = if api_key.is_some() {
            check_api_key_task = Some(AuthenticationService::new().check_api_key(link.callback(|valid| {
                Msg::OnAuthenticationChecked(valid)
            })));

            route_service.get_route()
        } else {
            route_service.set_route("/login", ());
            AppRoute::Login.into()
        };

        let router = RouteAgent::bridge(link.callback(|route| Msg::OnRouteChanged(route)));
        let menu_agent = MenuAgent::bridge(link.callback(|data| Msg::OnMenuAgentResponse(data)));
        let translator = Translator::new();

        JinyaDesignerApp {
            link,
            route_service,
            route,
            check_api_key_task,
            router,
            menu_agent,
            menu_title: translator.translate("app.title.home_page"),
            translator,
            hide_search: false,
        }
    }

    fn update(&mut self, msg: Self::Message) -> bool {
        match msg {
            Msg::OnRouteChanged(route) => {
                self.route = route;
                self.hide_search = false;
            }
            Msg::OnAuthenticationChecked(valid) => if !valid {
                self.router.send(RouteRequest::ChangeRoute(Route::from(AppRoute::Login)));
            },
            Msg::Ignore => {}
            Msg::OnMenuAgentResponse(response) => {
                match response {
                    MenuAgentResponse::OnChangeTitle(title) => self.menu_title = title,
                    MenuAgentResponse::OnHideSearch => {
                        self.hide_search = true;
                    }
                    _ => {}
                }
            }
            Msg::OnMenuKeyword(value) => self.menu_agent.send(MenuAgentRequest::Keyword(value)),
            Msg::OnMenuSearch(value) => self.menu_agent.send(MenuAgentRequest::Search(value)),
            Msg::OnChangePassword(event) => {
                event.prevent_default();
            }
            Msg::OnLogout(event) => {
                event.prevent_default();
                AuthenticationStorage::clear_api_key();
                AuthenticationStorage::clear_roles();
                AuthenticationStorage::clear_device_code();
                self.router.send(RouteRequest::ChangeRoute(Route::from(AppRoute::Login)));
            }
        }

        true
    }

    fn change(&mut self, _props: Self::Properties) -> bool {
        false
    }

    fn view(&self) -> Html {
        if self.route_service.get_route().route.eq("/login") {
            html! {
                <LoginPage />
            }
        } else if self.route_service.get_route().route.eq("/two-factor") {
            html! {
                <TwoFactorPage />
            }
        } else {
            html! {
                <main>
                    {self.get_menu()}
                    <Router<AppRoute, ()>
                        render=self.router_render()
                        redirect=Router::redirect(|route: Route| {
                            AppRoute::Homepage
                        })
                    />
                </main>
            }
        }
    }
}

impl JinyaDesignerApp {
    fn get_menu(&self) -> Html {
        let content_group = vec![
            SubItemGroup {
                title: self.translator.translate("app.menu.content.media"),
                items: vec![
                    SubItem {
                        label: self.translator.translate("app.menu.content.media.files"),
                        route: Some(&AppRoute::Files),
                        on_click: None,
                    },
                    SubItem {
                        label: self.translator.translate("app.menu.content.media.galleries"),
                        route: Some(&AppRoute::Galleries),
                        on_click: None,
                    },
                ],
            },
            SubItemGroup {
                title: self.translator.translate("app.menu.content.pages"),
                items: vec![
                    SubItem {
                        label: self.translator.translate("app.menu.content.pages.simple_pages"),
                        route: Some(&AppRoute::SimplePages),
                        on_click: None,
                    },
                    SubItem {
                        label: self.translator.translate("app.menu.content.pages.segment_pages"),
                        route: Some(&AppRoute::SegmentPages),
                        on_click: None,
                    },
                ],
            },
        ];
        let my_jinya_group = vec![
            SubItemGroup {
                title: self.translator.translate("app.menu.my_jinya.my_account"),
                items: vec![
                    SubItem {
                        label: self.translator.translate("app.menu.my_jinya.my_account.my_profile"),
                        route: Some(&AppRoute::MyProfile),
                        on_click: None,
                    },
                    SubItem {
                        label: self.translator.translate("app.menu.my_jinya.my_account.change_password"),
                        route: None,
                        on_click: Some(self.link.callback(|event| Msg::OnChangePassword(event))),
                    },
                    SubItem {
                        label: self.translator.translate("app.menu.my_jinya.my_account.logout"),
                        route: None,
                        on_click: Some(self.link.callback(|event| Msg::OnLogout(event))),
                    },
                ],
            },
        ];

        let placeholder = if self.hide_search {
            None
        } else {
            Some(self.translator.translate("app.menu.search"))
        };

        html! {
            <div style="position: sticky; top: 0; z-index: 1;">
                <MenuBar search_placeholder=placeholder title=&self.menu_title on_search=self.link.callback(|value| Msg::OnMenuSearch(value)) on_keyword=self.link.callback(|value| Msg::OnMenuKeyword(value))>
                    <MenuItem<AppRoute> groups=content_group label=self.translator.translate("app.menu.content") />
                    <MenuItem<AppRoute> groups=my_jinya_group label=self.translator.translate("app.menu.my_jinya") />
                </MenuBar>
            </div>
        }
    }
    fn router_render(&self) -> Render<AppRoute, ()> {
        Router::render(|switch: AppRoute| {
            match switch {
                AppRoute::Login => html! {<LoginPage />},
                AppRoute::TwoFactor => html! {<TwoFactorPage />},
                AppRoute::Homepage => html! {<HomePage />},
                AppRoute::Files => html! {<FilesPage />},
                AppRoute::Galleries => html! {<GalleriesPage />},
                AppRoute::GalleryDesigner(id) => html! {<GalleryDesignerPage id=id />},
                AppRoute::SimplePages => html! {<SimplePagesPage />},
                AppRoute::EditSimplePage(id) => html! {<EditSimplePagePage id=id />},
                AppRoute::AddSimplePage => html! {<AddSimplePagePage />},
                AppRoute::SegmentPages => html! {<SegmentPagesPage />},
                AppRoute::SegmentPageDesigner(id) => html! {<SegmentPageDesignerPage id=id />},
                AppRoute::MyProfile => html! {}
            }
        })
    }
}