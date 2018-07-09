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
    return (
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
          <div className="numbersBoxUse">
            <h2>62</h2>
            <p>Instituições que usam</p>
          </div>
          <div className="numbersBoxStates">
            <h2>14</h2>
            <p>Estados atendidos</p>
          </div>
        </div>
        <div id="map"></div>
      </Container>
    );
  }
}

module.exports = MapBlock;
