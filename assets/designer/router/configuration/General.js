import ArtistsOverview from '@/components/Configuration/General/Artists/Overview';
import AddArtist from '@/components/Configuration/General/Artists/Add';
import EditArtist from '@/components/Configuration/General/Artists/Edit';
import Routes from '@/router/Routes';
import MyJinya from '@/components/MyJinya/MyJinya';

export default [
  {
    path: Routes.Configuration.General.Artists.Overview.route,
    name: Routes.Configuration.General.Artists.Overview.name,
    component: ArtistsOverview,
    meta: {
      title: 'routes.configuration.general.artists.overview',
      searchEnabled: true,
    },
  },
  {
    path: Routes.Configuration.General.Artists.Add.route,
    name: Routes.Configuration.General.Artists.Add.name,
    component: AddArtist,
    meta: {
      title: 'routes.configuration.general.artists.add',
    },
  },
  {
    path: Routes.Configuration.General.Artists.Details.route,
    name: Routes.Configuration.General.Artists.Details.name,
    component: MyJinya,
    meta: {
      title: 'routes.configuration.general.artists.details',
    },
  },
  {
    path: Routes.Configuration.General.Artists.Edit.route,
    name: Routes.Configuration.General.Artists.Edit.name,
    component: EditArtist,
    meta: {
      title: 'routes.configuration.general.artists.edit',
    },
  },
];
