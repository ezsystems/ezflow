<?php /*

[General]
AllowedTypes[]
#AllowedTypes[]=Example
AllowedTypes[]=Manual
AllowedTypes[]=Manual3Items
AllowedTypes[]=Manual4Items
AllowedTypes[]=Manual5Items
AllowedTypes[]=Gallery
AllowedTypes[]=Video
AllowedTypes[]=ItemizedList
AllowedTypes[]=VerticallyListedItems
AllowedTypes[]=MainStory
AllowedTypes[]=Banner
AllowedTypes[]=Dynamic3Items
AllowedTypes[]=TagCloud
AllowedTypes[]=Poll

#[Example]
## Name of the block type as shown in the editorial interface.
#Name=Fetch Name Shown In Editorial Interface
## How many items are valid, as the new ones are being added, the oldest ones
## are moved to archive (or moved to another block) so that in any moment,
## max. NumberOfValidItems are valid.
#NumberOfValidItems=10
#NumberOfArchivedItems=5
#ManualAddingOfItems=disabled
#TTL=86400
#FetchClass=ezmExample
#FetchFixedParameters[]
#FetchFixedParameters[Class]=article;folder
#FetchFixedParameters[...]=...
#FetchParameters[]
#FetchParametersIsRequired[]
FetchParameters[Source]=NodeID
#FetchParametersIsRequired[Source]=true
# single / multiple
FetchParametersSelectionType[Source]=single
# true / false
FetchParametersIsRequired[Source]=true
#FetchParameters[...]=string
#FetchParameters[...]=integer
#CustomAttributes[]=node_id
#UseBrowseMode[node_id]=true
#ViewList[]=variation1
#ViewName[variation1]=Main sotry 1
#

[Manual]
Name=Default (Manual)
NumberOfValidItems=2
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=default
ViewName[default]=Default view

[Manual3Items]
Name=3 items (Manual)
NumberOfValidItems=3
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=3_items
ViewName[3_items]=3 items (1)

[Manual4Items]
Name=4 items (Manual)
NumberOfValidItems=4
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=4_items
ViewName[4_items]=4 items (1)

[Manual5Items]
Name=5 items (Manual)
NumberOfValidItems=5
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=5_items
ViewName[5_items]=5 items (1)

[Gallery]
Name=Gallery (Manual)
NumberOfValidItems=4
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=gallery
ViewName[gallery]=Fade 4 photos

[Video]
Name=Video (Manual)
NumberOfValidItems=1
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=video
ViewName[video]=Video player

[ItemizedList]
Name=Itemized List
NumberOfValidItems=5
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=itemizedlist
ViewName[itemizedlist]=Itemized List

[VerticallyListedItems]
Name=Vertically Listed Items
NumberOfValidItems=5
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=verticallylisteditems
ViewName[verticallylisteditems]=Vertically listed items

[MainStory]
Name=Main story (Manual)
NumberOfValidItems=1
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=main_story1
ViewList[]=main_story2
ViewName[main_story1]=Main story (1)
ViewName[main_story2]=Main story (2)

[Banner]
Name=Banner
NumberOfValidItems=1
NumberOfArchivedItems=5
ManualAddingOfItems=enabled
ViewList[]=banner
ViewName[banner]=Banner


[Dynamic3Items]
Name=3 items (dynamic)
NumberOfValidItems=3
NumberOfArchivedItems=5
ManualAddingOfItems=disabled
FetchClass=ezmLatestObjects
FetchFixedParameters[]
FetchFixedParameters[Class]=article
FetchParameters[]
FetchParametersIsRequired[]
FetchParameters[Source]=nodeID
# Single / Multiple
FetchParametersSelectionType[Source]=single
# True / False
FetchParametersIsRequired[Source]=true
TemplateList[]=3d_items.tpl
TemplateList[]=3d_list.tpl

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


*/ ?>
