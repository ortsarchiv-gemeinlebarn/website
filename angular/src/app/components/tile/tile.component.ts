import { Component, Input, OnInit } from '@angular/core';

@Component({
    selector: 'oag-tile',
    templateUrl: './tile.component.html',
    styleUrls: ['./tile.component.scss']
})
export class TileComponent implements OnInit
{
    @Input() public width = 4;
    @Input() public height = 4;

    @Input() public title = '';
    @Input() public link = null;

    @Input() public backgroundImage = null;
    @Input() public backgroundColor = null;

    constructor() { }
    public ngOnInit() { }
}
