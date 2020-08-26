import { Component, OnInit } from '@angular/core';
import { StatsApiService } from 'src/app/services/stats-api/stats-api.service';

@Component({
    selector: 'oag-hero',
    templateUrl: './hero.component.html',
    styleUrls: ['./hero.component.scss']
})
export class HeroComponent implements OnInit
{
    public countAnimationGDuration = 1300;
    public countAnimationStep = 50;
    public steps = this.countAnimationGDuration / this.countAnimationStep;

    public stats = {
        eintraege: {
            to: 0,
            label: 0
        },
        digitalisate: {
            to: 0,
            label: 0
        },
        eintraegeDigitalisierungsgrad: {
            to: 0,
            label: 0
        }
    };

    constructor(public statsAPI: StatsApiService) { }
    public async ngOnInit()
    {
        this.statsAPI.read().subscribe(async (stats) =>
        {
            this.stats.eintraege.to = stats.eintraege;
            this.stats.digitalisate.to = stats.digitalisate;
            this.stats.eintraegeDigitalisierungsgrad.to = stats.eintraegeDigitalisierungsgrad;

            this.animateStat(this.stats.eintraege);
            this.animateStat(this.stats.digitalisate);
            this.animateStat(this.stats.eintraegeDigitalisierungsgrad);
        });
    }

    public async animateStat(stat)
    {
        const stepSize = stat.to / this.steps;

        for (let step = 0; step < this.steps; step++)
        {
            await new Promise((resolve) => setTimeout(() =>
            {
                stat.label += stepSize;
                resolve();
            }, this.countAnimationStep));
        }
    }
}
