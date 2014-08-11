			<div id="nameDiv" class="tr clearfix">
				<label>Name:</label>
				<div><?=$this->getName?></div>
			</div>
			
			<div class="clearfix">
				<div class="sidebar left">
					<h2 class="headerbar hbDark">Traits &amp; Skills</h2>
					<div id="primaryTraits">
<?
	foreach (savageworlds_consts::getTraits() as $abbrev => $label) {
		$dice = $this->getTraits($abbrev);
?>
						<div class="hbdMargined traitDiv" data-trait="<?=$abbrev?>">
							<div class="trait clearfix">
								<div class="traitName"><?=$label?></div>
								<div class="diceSelect">d<?=$dice?></div>
							</div>
							<div class="skillHeader"><?=$label?> Skills</div>
							<div class="skills">
<?		$this->displaySkills($abbrev); ?>
							</div>
						</div>
<?	} ?>
					</div>
					
					<div id="derivedTraits">
<?	foreach (array('Pace', 'Parry', 'Charisma', 'Toughness') as $derivedTrait) { ?>
						<div class="tr">
							<label class="traitName"><?=$derivedTrait?></label>
							<div class="traitValue boxedValue borderBox"><?=$this->getDerivedTraits(strtolower($derivedTrait))?></div>
						</div>
<?	} ?>
					</div>
				</div>
				<div class="mainColumn right">
					<div class="clearfix">
						<div class="twoCol">
							<h2 class="headerbar hbDark">Edges &amp; Hindrances</h2>
							<div class="hbdMargined"><?=printReady($this->getEdgesHindrances())?></div>
						</div>
						<div class="twoCol lastTwoCol">
							<h2 class="headerbar hbDark">Injuries</h2>
							<div id="injNums" class="hbdMargined">
								<div>
									<div>Wounds</div>
									<div class="boxedValue borderBox"><?=$this->getWounds()?></div>
								</div>
								<div>
									<div>Fatigue</div>
									<div class="boxedValue borderBox"><?=$this->getFatigue()?></div>
								</div>
							</div>
							<div class="hbdMargined"><?=printReady($this->getInjuries())?></div>
						</div>
					</div>
						
					<div class="clearfix">
						<div class="twoCol">
							<h2 class="headerbar hbDark">Shootin Irons &amp; Such</h2>
							<div class="hbdMargined"><?=printReady($this->getWeapons())?></div>
						</div>
						<div class="twoCol lastTwoCol">
							<h2 class="headerbar hbDark">Equipment</h2>
							<div class="hbdMargined"><?=printReady($this->getEquipment())?></div>
						</div>
					</div>
					
					<h2 class="headerbar hbDark">Background/Notes</h2>
					<div class="hbdMargined"><?=printReady($this->getNotes())?></div>
				</div>
			</div>