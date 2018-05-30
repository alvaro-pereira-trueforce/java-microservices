import { NgModule }             from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HowToInstallPageComponent } from './how-to-install-page/how-to-install-page.component';
import { FAQPageComponent } from './faqpage/faqpage.component';

const instagramRoutes: Routes = [
  { path: 'how-to-install',  component: HowToInstallPageComponent },
  { path: 'faq', component: FAQPageComponent },
  { path: '**', component: HowToInstallPageComponent }
];

@NgModule({
  imports: [
    RouterModule.forChild(instagramRoutes)
  ],
  exports: [
    RouterModule
  ]
})
export class InstagramRoutingModule { }