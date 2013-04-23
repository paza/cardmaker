# About
A simple tool to create Cards for a cardsort deck.

# Features

- Create PDF with cards from a list of strings
- Choose between 1, 2, 4 and 8 cards per page
- Add cropmarks
- Add card numbers
- Align text left or centered
- [new] Use HTML to format your cards

# Requirements
- PHP
- Composer, install instructions: http://getcomposer.org/download/

# Licence
The software and media in the main folder are distributed under the terms of the WTF Public Licence expressed in the document LICENCE

The Software and Data in the folder tfpdf is distributed under its respective licence.

# SETUP

```sh
# Install dependencies
composer install

# Add analytics code to your installation (optional)
cp src/analytics.html.dist src/analytics.html
```

# Use as a service

## Example

```sh
curl -d "cards=Card one---Card two---Card three---Card four---Card five---Card six---Card seven---Card eight---Card one on second page" http://cardmaker.patrickzahnd.ch/cards.pdf > test.pdf
```

## Parameters

- (string) cards : mandatory
  - The card strings to be printed
  - Possible delimiters are \n and ---
- (int) cardsPerPage : optional
  - Number of cards to print per page
  - Possible values are 1, 2, 4 and 8
- (int) showNumbers : optional
  - Print numbers for each card
  - Possible value is 1
- (int) cropmark : optional
  - Print cropmarks
  - Possible value is 1
- (int) html : optional
  - Enable HTML support
  - Possible value is 1
- (char) alignment : optional
  - Set the alignment for the text
  - Possible values are 
    - L for left and 
    - C for center