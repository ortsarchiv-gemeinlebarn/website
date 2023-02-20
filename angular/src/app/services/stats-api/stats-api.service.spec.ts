import { TestBed } from '@angular/core/testing';

import { StatsApiService } from './stats-api.service';

describe('StatsApiService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: StatsApiService = TestBed.get(StatsApiService);
    expect(service).toBeTruthy();
  });
});
