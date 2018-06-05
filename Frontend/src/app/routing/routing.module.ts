import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { appRoutes } from './routes';

@NgModule({
  imports: [
    RouterModule.forRoot(
        appRoutes, {
          enableTracing: false,
          useHash: true
        }
    ),
    CommonModule
  ],
  exports: [
    RouterModule
  ],
  declarations: []
})
export class RoutingModule {
}
