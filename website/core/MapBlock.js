/**
 * Copyright (c) 2017-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */
const React = require('react');
const classNames = require('classnames');
const CompLibrary = require(process.cwd()+'/node_modules/docusaurus/lib/core/CompLibrary.js');
const utils = require(process.cwd()+'/node_modules/docusaurus/lib/core/utils.js');
const siteConfig = require(process.cwd() + '/siteConfig.js');
const MarkdownBlock = CompLibrary.MarkdownBlock;
const GridBlock = CompLibrary.GridBlock;
const Container = CompLibrary.Container;




class MapBlock extends React.Component{

  render() {
    var render = (
      <Container
        padding={['bottom', 'top']}
        id={this.props.id}
        background={this.props.background}
        className={this.props.className}>
        <div className="numbers">
          <GridBlock className="mapsBlockHeader" align="left" contents={[
            {
              title: 'O i-Educar em números',
              textAlign: 'left',
              content: 'O i-Educar ajuda várias instituições a administrarem seu dia-a-dia e a economizarem em seus negócios. Descubra os números.'
            }
          ]} layout="OneColumn" />
          <div className="numbersBoxCities">
            <h2>{this.props.cities}</h2>
            <p>Municípios que usam</p>
          </div>
          <div className="numbersBoxSchools">
            <h2>{this.props.schools}</h2>
            <p>Escolas atendidas</p>
          </div>
          <div className="numbersBoxStudents">
            <h2>{this.props.students}</h2>
            <p>Alunos atingidos</p>
          </div>
        </div>
        <div id="map"></div>
        <script src={"https://maps.googleapis.com/maps/api/js?key="+this.props.apikey+"&callback=initMap"}></script>
      </Container>
    );
    return render;
  }
}
module.exports = MapBlock;
