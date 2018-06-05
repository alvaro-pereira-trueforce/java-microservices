import { Component, Input, OnInit } from '@angular/core';
import { TemplateRef } from '@angular/core';

@Component({
  selector: 'nav-bar',
  templateUrl: './nav-bar.component.html',
  styleUrls: [ './nav-bar.component.scss' ]
})
export class NavBarComponent implements OnInit {

  @Input()
  navBarTemplate: TemplateRef<any>;
  constructor() {
  }

  ngOnInit() {
  }

}
