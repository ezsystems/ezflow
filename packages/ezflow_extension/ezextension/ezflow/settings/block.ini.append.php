<?php /*

[General]
#AllowedTypes[]=Example
AllowedTypes[]=Manual2Items
AllowedTypes[]=Manual3Items
AllowedTypes[]=Manual4Items
AllowedTypes[]=Manual5Items
AllowedTypes[]=Dynamic3Items
AllowedTypes[]=Gallery
AllowedTypes[]=Video
AllowedTypes[]=ItemList
AllowedTypes[]=MainStory
AllowedTypes[]=Banner
AllowedTypes[]=TagCloud
AllowedTypes[]=Poll
AllowedTypes[]=FlashRecorder
AllowedTypes[]=FeedReader
AllowedTypes[]=Keywords
AllowedTypes[]=GMap
AllowedTypes[]=OnlineUsers

#[Example]
# Name of the block type as shown in the editorial interface.
# Name=Fetch Name Shown In Editorial Interface
# How many items are valid, as the new ones are being added, the oldest ones
# are moved to archive (or moved to another block) so that in any moment,
# max. NumberOfValidItems are valid.
# NumberOfValidItems=10
# NumberOfArchivedItems=5
# ManualAddingOfItems=disabled
# TTL=86400
# FetchClass=ezmExample
# FetchFixedParameters[]
# FetchFixedParameters[Class]=article;folder
# FetchFixedParameters[...]=...
# FetchParameters[]
# FetchParametersIsRequired[]
# FetchParameters[Source]=NodeID
# FetchParametersIsRequired[Source]=true
# single / multiple
# FetchParametersSelectionType[Source]=single
# true / false
# FetchParametersIsRequired[Source]=true
# FetchParameters[...]=string
# FetchParameters[...]=integer
# CustomAttributes[]=node_id
# UseBrowseMode[node_id]=true
# ViewList[]=variation1
# ViewName[variation1]=Main sotry 1
#
# Used by browse mode for manual block,
# possibility to limit block items to specific class
# AllowedClasses[]=article

[Manual2Items]
Name=2 items (Manual)
NumberOfValidItems=2
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=2_items1
ViewList[]=2_items2
ViewName[2_items1]=2 items (1)
ViewName[2_items2]=2 items (2)

[Manual3Items]
Name=3 items (Manual)
NumberOfValidItems=3
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=3_items1
ViewList[]=3_items2
ViewList[]=3_items3
ViewName[3_items1]=3 items (1)
ViewName[3_items2]=3 items (2)
ViewName[3_items3]=3 items (3 cols)

[Manual4Items]
Name=4 items (Manual)
NumberOfValidItems=4
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=4_items1
ViewList[]=4_items2
ViewList[]=4_items3
ViewName[4_items1]=4 items (1)
ViewName[4_items2]=4 items (2)
ViewName[4_items3]=4 items (3)

[Manual5Items]
Name=5 items (Manual)
NumberOfValidItems=5
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=5_items1
ViewList[]=5_items2
ViewName[5_items1]=5 items (1)
ViewName[5_items2]=5 items (2)

[Dynamic3Items]
Name=3 items (Dynamic)
NumberOfValidItems=3
NumberOfArchivedItems=5
ManualAddingOfItems=disabled
FetchClass=eZFlowLatestObjects
FetchFixedParameters[Class]=article
FetchFixedParameters[Source]=69
ViewList[]=3_items1
ViewName[3_items1]=3 items (1)

[Gallery]
Name=Gallery (Manual)
NumberOfValidItems=4
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=gallery1
ViewName[gallery1]=Gallery (1)

[Video]
Name=Video (Manual)
NumberOfValidItems=1
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=video
ViewName[video]=Video player

[ItemList]
Name=Item list
NumberOfValidItems=12
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=itemlist1
ViewList[]=itemlist2
ViewList[]=itemlist3
ViewName[itemlist1]=List (1 col)
ViewName[itemlist2]=List (2 cols)
ViewName[itemlist3]=List (3 cols)

[MainStory]
Name=Main story (Manual)
NumberOfValidItems=1
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=main_story1
ViewList[]=main_story2
ViewList[]=main_story3
ViewName[main_story1]=Main story (1)
ViewName[main_story2]=Main story (2)
ViewName[main_story3]=Main story (3)

[Banner]
Name=Banner
NumberOfValidItems=1
NumberOfArchivedItems=5
ManualAddingOfItems=disabled
ViewList[]=banner1
ViewList[]=banner2
ViewName[banner1]=Banner (medium)
ViewName[banner2]=Banner (small)

[TagCloud]
Name=Tag cloud
ManualAddingOfItems=disabled
CustomAttributes[]=subtree_node_id
UseBrowseMode[subtree_node_id]=true
ViewList[]=tag_cloud
ViewName[tag_cloud]=Tag cloud

[Poll]
Name=Poll
ManualAddingOfItems=disabled
CustomAttributes[]=poll_node_id
UseBrowseMode[poll_node_id]=true
ViewList[]=poll
ViewName[poll]=Poll

[FlashRecorder]
Name=Flash Recorder
NumberOfValidItems=8
NumberOfArchivedItems=4
ManualAddingOfItems=enabled
ViewList[]=flash_recorder
ViewName[flash_recorder]=Flash Recorder

[FeedReader]
Name=Feed reader
ManualAddingOfItems=disabled
CustomAttributes[]=source
CustomAttributes[]=limit
CustomAttributes[]=offset
ViewList[]=feed_reader
ViewName[feed_reader]=Feed reader

[Keywords]
Name=Keywords
NumberOfValidItems=5
NumberOfArchivedItems=5
ManualAddingOfItems=disabled
FetchClass=eZFlowKeywordsFetch
FetchFixedParameters[Class]=article
FetchParameters[Source]=NodeID
FetchParametersSelectionType[Source]=single
FetchParametersIsRequired[Source]=true
FetchParameters[Keywords]=string
ViewList[]=keywords
ViewName[keywords]=Keywords

[GMap]
Name=Google Map
ManualAddingOfItems=disabled
CustomAttributes[]=location
CustomAttributes[]=key
ViewList[]=gmap
ViewName[gmap]=Google Map

[OnlineUsers]
Name=Online Users
ManualAddingOfItems=disabled
ViewList[]=onlineusers
ViewName[onlineusers]=Online Users

*/ ?>
