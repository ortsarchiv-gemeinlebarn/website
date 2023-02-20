import { Component, Input, OnInit } from '@angular/core';

@Component({
    selector: 'oag-stat-tile',
    templateUrl: './stat-tile.component.html',
    styleUrls: ['./stat-tile.component.scss']
})
export class StatTileComponent implements OnInit
{
    @Input() public color: string = "#ffffff";

    @Input() public to: number;
    public label: string = "";
    public count: number = 0;

    @Input() public decimals: number = 0;
    @Input() public suffix: string = "";
    @Input() public name: string = "";

    public countAnimationGDuration = 800;
    public countAnimationStep = 50;
    public steps = this.countAnimationGDuration / this.countAnimationStep;

    constructor() { }

    public async ngOnInit()
    {
        await this.animateStat();
    }

    public async animateStat()
    {
        const zeroDigits = this.to.toString().length;
        const stepSize = this.to / this.steps;

        for (let step = 0; step < this.steps; step++)
        {
            await new Promise<void>((resolve) => setTimeout(() =>
            {
                this.count += stepSize;
                this.label = this.format(this.count, zeroDigits, this.decimals);
                resolve();
            }, this.countAnimationStep));
        }
    }

    public format = (num, zeroDigits, decimals) => (Math.round(num * 100) / 100).toFixed(decimals).toString().padStart(zeroDigits, '0');
}
