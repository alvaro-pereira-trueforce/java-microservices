import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SideMenuComponent } from './side-menu.component';
import { RouterModule } from '@angular/router';

@NgModule({
  imports: [
    CommonModule,
    RouterModule
  ],
  exports: [
    SideMenuComponent,
    RouterModule
  ],
  declarations: [ SideMenuComponent ]
})
export class SideMenuModule {
}
