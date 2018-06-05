import { Component, OnInit, TemplateRef, ViewChild } from '@angular/core';

@Component({
  selector: 'index',
  templateUrl: './index.component.html',
  styleUrls: ['./index.component.scss']
})
export class IndexComponent implements OnInit {

  @ViewChild('navBarTemplate')
  public navBarTemplate: TemplateRef<any>;

  constructor() { }

  ngOnInit() {
  }

}
