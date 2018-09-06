import { NgModule }             from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { HowToInstallPageComponent } from './how-to-install-page/how-to-install-page.component';
import { FAQPageComponent } from './faqpage/faqpage.component';
import { IndexComponent } from './index/index.component';

const instagramRoutes: Routes = [
  {
    path: 'instagram',
    component: IndexComponent,
    children:[
      { path: 'how-to-install',  component: HowToInstallPageComponent },
      { path: 'faq', component: FAQPageComponent },
      {
        path: '',
        redirectTo: 'how-to-install',
        pathMatch: 'full'
      }
    ]
  }
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
