import { Component, HostBinding, OnInit } from '@angular/core';
import { slideInDownAnimation } from '../../animations/Router';

@Component({
  selector: 'app-faqpage',
  templateUrl: './faqpage.component.html',
  styleUrls: ['./faqpage.component.scss'],
  animations: [ slideInDownAnimation ]
})
export class FAQPageComponent implements OnInit {

  @HostBinding('@routeAnimation') routeAnimation = true;
  @HostBinding('style.display')   display = 'block';
  @HostBinding('style.position')  position = 'absolute';

  constructor() { }

  ngOnInit() {
  }

}
