import { NgModule } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';

import { HttpClientModule } from '@angular/common/http';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { ColumnComponent } from './components/column/column.component';
import { HeaderComponent } from './components/header/header.component';
import { HeroComponent } from './components/hero/hero.component';
import { RowComponent } from './components/row/row.component';
import { SectionComponent } from './components/section/section.component';
import { TileComponent } from './components/tile/tile.component';
import { HomePageComponent } from './pages/home-page/home-page.component';
import { TaetigkeitenComponent } from './pages/taetigkeiten/taetigkeiten.component';
import { FooterComponent } from './components/footer/footer.component';

@NgModule({
    declarations: [
        AppComponent,
        HomePageComponent,
        TileComponent,
        HeaderComponent,
        HeroComponent,
        RowComponent,
        ColumnComponent,
        SectionComponent,
        TaetigkeitenComponent,
        FooterComponent
    ],
    imports: [
        BrowserModule,
        AppRoutingModule,
        HttpClientModule
    ],
    providers: [],
    bootstrap: [AppComponent]
})
export class AppModule { }
