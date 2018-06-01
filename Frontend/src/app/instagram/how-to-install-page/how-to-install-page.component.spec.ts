import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { HowToInstallPageComponent } from './how-to-install-page.component';

describe('HowToInstallPageComponent', () => {
  let component: HowToInstallPageComponent;
  let fixture: ComponentFixture<HowToInstallPageComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ HowToInstallPageComponent ]
    })
        .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(HowToInstallPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
