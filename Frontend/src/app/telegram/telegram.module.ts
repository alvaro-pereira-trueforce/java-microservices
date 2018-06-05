import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { IndexComponent } from './index/index.component';

@NgModule({
  imports: [
    CommonModule
  ],
  exports: [ IndexComponent ],
  declarations: [ IndexComponent ]
})
export class TelegramModule {
}
