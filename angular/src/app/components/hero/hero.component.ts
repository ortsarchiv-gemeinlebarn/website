import { Component, OnInit } from '@angular/core';
import { StatsApiService } from 'src/app/services/stats-api/stats-api.service';

@Component({
    selector: 'oag-hero',
    templateUrl: './hero.component.html',
    styleUrls: ['./hero.component.scss']
})
export class HeroComponent implements OnInit
{
    public years = (new Date).getFullYear() - 1985;

    public stats = {
        fonds: 65,
        series: 68,
        files: 507,
        documents: 9015,
        digitalEditions: 8654,
        digitalEditionPages: 15948,
        digitalisierungsgrad: 94.1430
    };

    constructor() { }
    public ngOnInit() { }
}
