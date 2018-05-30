import { Routes } from '@angular/router';
import { IndexComponent } from '../home/index/index.component';
import { AppComponent } from '../app.component';

export const appRoutes: Routes = [ {
  path: '',
  component: IndexComponent
}, {
  path: 'page1',
  component: AppComponent
}];