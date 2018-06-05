import { Routes } from '@angular/router';
import * as home from '../home/index/index.component';
import * as telegram from '../telegram/index/index.component';

export const appRoutes: Routes = [ {
  path: '',
  redirectTo: '/telegram',
  pathMatch: 'full'
}, {
  path: '',
  component: home.IndexComponent
}, {
  path: 'telegram',
  component: telegram.IndexComponent
} ];