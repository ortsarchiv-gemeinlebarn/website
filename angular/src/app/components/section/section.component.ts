import { Component, Input, OnInit } from '@angular/core';

@Component({
    selector: 'oag-section',
    templateUrl: './section.component.html',
    styleUrls: ['./section.component.scss']
})
export class SectionComponent implements OnInit
{

    @Input() public backgroundColor = null;
    constructor() { }

    public ngOnInit()
    {
    }

}
