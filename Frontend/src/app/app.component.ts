import { Component, TemplateRef } from '@angular/core';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: [ './app.component.scss' ]
})
export class AppComponent {

  navBarTemplate: TemplateRef<any>;
  componentAdded(component)
  {
    this.navBarTemplate = component.navBarTemplate;
    window.scroll(0,0);
  }
}
