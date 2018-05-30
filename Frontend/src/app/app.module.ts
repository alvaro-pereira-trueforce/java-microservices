import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';

import { AppComponent } from './app.component';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { RouterModule, Routes } from '@angular/router';

const appRoutes: Routes = [
    {
        path: '**',
        component: AppComponent
    }
];

@NgModule({
    declarations: [
        AppComponent
    ],
    imports: [
        RouterModule.forRoot(
            appRoutes, {
                enableTracing: false,
                useHash: true
            }
        ),
        BrowserModule,
        NgbModule.forRoot()
    ],
    providers: [],
    bootstrap: [ AppComponent ]
})

export class AppModule {
}
