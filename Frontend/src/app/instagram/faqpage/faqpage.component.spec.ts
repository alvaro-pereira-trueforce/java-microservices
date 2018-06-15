import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FAQPageComponent } from './faqpage.component';

describe('FAQPageComponent', () => {
  let component: FAQPageComponent;
  let fixture: ComponentFixture<FAQPageComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FAQPageComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FAQPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
