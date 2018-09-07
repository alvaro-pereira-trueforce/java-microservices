import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SideMenuComponent } from './side-menu.component';
import { RouterModule } from '@angular/router';
import { NgbCollapseModule } from "@ng-bootstrap/ng-bootstrap";
import { FontAwesomeModule } from "@fortawesome/angular-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faTasks } from "../../../node_modules/@fortawesome/free-solid-svg-icons/faTasks";
import { faQuestionCircle } from "@fortawesome/free-solid-svg-icons";

library.add(faTasks);
library.add(faQuestionCircle);

@NgModule({
  imports: [
    CommonModule,
    RouterModule,
    NgbCollapseModule,
    FontAwesomeModule
  ],
  exports: [
    SideMenuComponent,
    RouterModule
  ],
  declarations: [ SideMenuComponent ]
})
export class SideMenuModule {
}
