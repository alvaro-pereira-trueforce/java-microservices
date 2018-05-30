import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { HowToInstallPageComponent } from './how-to-install-page/how-to-install-page.component';
import { FAQPageComponent } from './faqpage/faqpage.component';
import { InstagramRoutingModule } from './instagram-routing.module';


@NgModule({
  imports: [
    CommonModule,
    InstagramRoutingModule
  ],
  declarations: [ HowToInstallPageComponent, FAQPageComponent ]
})
export class InstagramModule {
}
