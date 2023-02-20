import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { TaetigkeitenComponent } from './taetigkeiten.component';

describe('TaetigkeitenComponent', () => {
  let component: TaetigkeitenComponent;
  let fixture: ComponentFixture<TaetigkeitenComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TaetigkeitenComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TaetigkeitenComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
