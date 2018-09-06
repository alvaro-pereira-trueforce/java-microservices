import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { SideMenuModule } from './side-menu/side-menu.module';
import { HomeModule } from './home/home.module';
import { NavBarModule } from './nav-bar/nav-bar.module';
import { RoutingModule } from './routing/routing.module';
import { InstagramModule } from './instagram/instagram.module';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { TelegramModule } from './telegram/telegram.module';

@NgModule({
  declarations: [
    AppComponent
  ],
  imports: [
    TelegramModule,
    InstagramModule,
    HomeModule,
    SideMenuModule,
    NavBarModule,
    RoutingModule,
    BrowserAnimationsModule,
    NgbModule,
    BrowserModule,
  ],
  providers: [],
  bootstrap: [ AppComponent ]
})

export class AppModule {
}
