import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { SideMenuModule } from './side-menu/side-menu.module';
import { HomeModule } from './home/home.module';
import { NavBarModule } from './nav-bar/nav-bar.module';
import { RoutingModule } from './routing/routing.module';

@NgModule({
  declarations: [
    AppComponent
  ],
  imports: [
    RoutingModule,
    BrowserModule,
    NgbModule.forRoot(),
    SideMenuModule,
    NavBarModule,
    HomeModule
  ],
  providers: [],
  bootstrap: [ AppComponent ]
})

export class AppModule {
}
