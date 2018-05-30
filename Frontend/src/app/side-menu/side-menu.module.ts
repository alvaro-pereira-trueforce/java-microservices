import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SideMenuComponent } from './side-menu.component';

@NgModule({
  imports: [
    CommonModule
  ],
  exports: [
    SideMenuComponent
  ],
  declarations: [ SideMenuComponent ]
})
export class SideMenuModule {
}
