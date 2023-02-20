import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { StatTileComponent } from './stat-tile.component';

describe('StatTileComponent', () => {
  let component: StatTileComponent;
  let fixture: ComponentFixture<StatTileComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ StatTileComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(StatTileComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
